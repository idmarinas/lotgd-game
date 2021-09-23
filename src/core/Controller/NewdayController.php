<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewdayController extends AbstractController
{
    use NewdayController\DragonPointSpendTrait;
    use NewdayController\NewDayTrait;
    use NewdayController\RecalculateDragonPointTrait;
    use NewdayController\SetRaceTrait;
    use NewdayController\SetSpecialtyTrait;

    private $translationDomain;
    private $translationDomainNavigation;
    private $settings;
    private $dispatcher;
    private $translator;
    private $dateTime;
    private $navigation;
    private $response;
    private $log;

    public function __construct(
        Settings $settings,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        DateTime $dateTime,
        Navigation $navigation,
        HttpResponse $response,
        Log $log
    ) {
        $this->settings   = $settings;
        $this->dispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->dateTime   = $dateTime;
        $this->navigation = $navigation;
        $this->response   = $response;
        $this->log        = $log;
    }

    public function index(Request $request): Response
    {
        global $session;

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_NEWDAY_INTERCEPT);
        modulehook('newday-intercept', $args->getArguments());

        $resurrection = (string) $request->query->get('resurrection');
        $resline      = ('true' == $resurrection) ? '&resurrection=true' : '';

        /*
         **  SETTINGS **
        */
        $turnsperday    = $this->settings->getSetting('turns', 10);
        $maxinterest    = ((float) $this->settings->getSetting('maxinterest', 10) / 100) + 1; //-- 1.1
        $mininterest    = ((float) $this->settings->getSetting('mininterest', 1) / 100)  + 1; //-- 1.1
        $dailypvpfights = $this->settings->getSetting('pvpday', 3);
        /*
         ** End Settings **
        */

        // Don't hook on to this text for your standard modules please, use "newday" instead.
        // This hook is specifically to allow modules that do other newdays to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_newday', 'textDomainNavigation' => 'navigation_newday']);
        $this->dispatcher->dispatch($args, Events::PAGE_NEWDAY_PRE);
        $result               = modulehook('newday-text-domain', $args->getArguments());
        $textDomain           = $result['textDomain'];
        $textDomainNavigation = $result['textDomainNavigation'];
        unset($result);

        $this->translationDomain           = $textDomain;
        $this->translationDomainNavigation = $textDomainNavigation;

        $params = [
            'textDomain'           => $textDomain,
            'includeTemplatesPre'  => [], //-- Templates that are in top of content (but below of title)
            'includeTemplatesPost' => [], //-- Templates that are in bottom of content
            'turnsPerDay'          => $turnsperday, //-- For compatibility
            'turns_per_day'        => $turnsperday,
            'max_interest'         => $maxinterest,
            'min_interest'         => $mininterest,
            'daily_pvp_fights'     => $dailypvpfights,
        ];

        //-- Change text domain for navigation
        $this->navigation->setTextDomain($textDomainNavigation);

        $this->navigation->addHeader('category.continue');

        //-- Asign dragon points if dragon points asign not is equal to dragon kills
        $this->asignDragonPoints($request);

        //-- Dragon Points labels and available points to buy
        $retargs = $this->dragonPointsLabels();
        $labels  = $retargs['desc'];
        $canbuy  = $retargs['buy'];

        $pdk    = $request->query->getInt('pdk');
        $dp     = \count($session['user']['dragonpoints']);
        $dkills = $session['user']['dragonkills'];

        (1 == $pdk) && $this->recalculateDragonPoints($labels, $dp, $request);

        if ($dp < $dkills)
        {
            $this->dragonPointSpend($params, $dkills, $dp, $canbuy, $resline);
        }
        elseif ( ! $session['user']['race'] || RACE_UNKNOWN == $session['user']['race'])
        {
            $this->setRace($request, $resline);
        }
        elseif ('' == $session['user']['specialty'])
        {
            $this->setSpecialty($request, $resline);
        }
        else
        {
            $this->newDay($params, $resurrection);
        }

        return $this->render('page/newday.html.twig', $params);
    }

    protected function render(string $template, array $params = [], ?Response $response = null): Response
    {
        //-- Restore text domain for navigation
        $this->navigation->setTextDomain();

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_NEWS_POST);
        $params = modulehook('page-newday-tpl-params', $args->getArguments());

        return parent::render($template, $params, $response);
    }

    protected function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    private function dragonPointsLabels(): array
    {
        $labels = [
            'general'    => 'General Stuff,title',
            'ff'         => 'Forest Fights + 1',
            'attributes' => 'Attributes,title',
            'str'        => 'Strength +1',
            'dex'        => 'Dexterity +1',
            'con'        => 'Constitution +1',
            'int'        => 'Intelligence +1',
            'wis'        => 'Wisdom +1',
            'unknown'    => 'Unknown Spends (contact an admin to investigate!)',
        ];

        /**
         * Use modulehook dkpointlabels to activate/desactivate labels or add more labels.
         */
        $canbuy = [
            'ff'      => 1,
            'str'     => 1,
            'dex'     => 1,
            'con'     => 1,
            'int'     => 1,
            'wis'     => 1,
            'unknown' => 0,
        ];

        if (is_module_active('staminasystem'))
        {
            $canbuy['ff'] = 0;
        }
        $args = new GenericEvent(null, ['desc' => $labels, 'buy' => $canbuy]);
        $this->dispatcher->dispatch($args, Events::PAGE_NEWDAY_DK_POINT_LABELS);

        return modulehook('dkpointlabels', $args->getArguments());
    }

    private function asignDragonPoints(Request $request)
    {
        global $session;

        $dk = $request->query->get('dk');

        if (\count($session['user']['dragonpoints']) < $session['user']['dragonkills'] && '' != $dk)
        {
            array_push($session['user']['dragonpoints'], $dk);

            switch ($dk)
            {
                case 'str':
                    $session['user']['strength']++;

                    break;
                case 'dex':
                    $session['user']['dexterity']++;

                    break;
                case 'con':
                    $session['user']['constitution']++;

                    break;
                case 'int':
                    $session['user']['intelligence']++;

                    break;
                case 'wis':
                    $session['user']['wisdom']++;

                    break;
                default:
            }
        }
    }
}
