<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Event\Core;
use Lotgd\Core\Event\Other;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Output\Color;
use Lotgd\Core\Pvp\Listing;
use Lotgd\Core\Tool\DateTime;
use Lotgd\Core\Tool\Sanitize;
use Lotgd\Core\Tool\Tool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InnController extends AbstractController
{
    private $navigation;
    private $dispatcher;
    private $translator;
    private $log;
    private $tool;
    private $sanitize;
    private $pvpListing;
    private $color;
    private $dateTime;
    private $settings;

    public function __construct(
        Navigation $navigation,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        Log $log,
        Tool $tool,
        Sanitize $sanitize,
        Listing $listing,
        Color $color,
        DateTime $dateTime,
        Settings $settings
    ) {
        $this->navigation = $navigation;
        $this->dispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->log        = $log;
        $this->tool       = $tool;
        $this->sanitize   = $sanitize;
        $this->pvpListing = $listing;
        $this->color      = $color;
        $this->dateTime   = $dateTime;
        $this->settings   = $settings;
    }

    public function converse(array $params): Response
    {
        $params['tpl'] = 'converse';

        $this->navigation->addHeader('category.other');
        $this->navigation->addNav('nav.return.inn', 'inn.php');

        return $this->renderInn($params);
    }

    public function bartender(array $params, Request $request): Response
    {
        global $session;

        $action = (string) $request->query->get('act');

        $params['tpl']    = 'bartender';
        $params['action'] = $action;

        if ('bribe' == $action)
        {
            $amt  = $request->query->getInt('amt');
            $type = (string) $request->query->get('type');

            $params['type']   = $type;
            $params['amount'] = $amt;

            $g1 = $session['user']['level'] * 10;
            $g2 = $session['user']['level'] * 50;
            $g3 = $session['user']['level'] * 100;

            if ('' == $type)
            {
                $this->navigation->addHeader($params['barkeep'], ['translate' => false]);
                $this->navigation->addNav('nav.bribe.gem', 'inn.php?op=bartender&act=bribe&type=gem&amt=1', [
                    'params' => [
                        'gem' => 1,
                    ],
                ]);
                $this->navigation->addNav('nav.bribe.gem', 'inn.php?op=bartender&act=bribe&type=gem&amt=2', [
                    'params' => [
                        'gem' => 2,
                    ],
                ]);
                $this->navigation->addNav('nav.bribe.gem', 'inn.php?op=bartender&act=bribe&type=gem&amt=3', [
                    'params' => [
                        'gem' => 3,
                    ],
                ]);

                $this->navigation->addNav('nav.bribe.gold', "inn.php?op=bartender&act=bribe&type=gold&amt={$g1}", [
                    'params' => [
                        'gold' => $g1,
                    ],
                ]);
                $this->navigation->addNav('nav.bribe.gold', "inn.php?op=bartender&act=bribe&type=gold&amt={$g2}", [
                    'params' => [
                        'gold' => $g2,
                    ],
                ]);
                $this->navigation->addNav('nav.bribe.gold', "inn.php?op=bartender&act=bribe&type=gold&amt={$g3}", [
                    'params' => [
                        'gold' => $g3,
                    ],
                ]);
            }
            else
            {
                if ('gem' == $type)
                {
                    if ($session['user']['gems'] < $amt)
                    {
                        $this->addFlash('warning', $this->translator->trans('flash.message.bribe.no.gems', ['amt' => $amt], $params['textDomain']));

                        $this->navigation->addNavAllow('inn.php?op=bartender&act=bribe');

                        return $this->redirect('inn.php?op=bartender&act=bribe');
                    }
                    else
                    {
                        $chance = $amt * 30;
                        $session['user']['gems'] -= $amt;

                        $this->log->debug("spent {$amt} gems on bribing {$params['barkeep']}");
                    }
                }
                elseif ($session['user']['gold'] < $amt)
                {
                    $this->addFlash('warning', $this->translator->trans('flash.message.bribe.no.gold', ['amt' => $amt], $params['textDomain']));
                    $this->navigation->addNavAllow('inn.php?op=bartender&act=bribe');

                    return $this->redirect('inn.php?op=bartender&act=bribe');
                }
                else
                {
                    $sfactor = 50   / 90;
                    $fact    = $amt / $session['user']['level'];
                    $chance  = ($fact - 10) * $sfactor + 25;
                    $session['user']['gold'] -= $amt;

                    $this->log->debug("spent {$amt} gold bribing {$params['barkeep']}");
                }

                $params['bribeSuccess'] = mt_rand(0, 100) < $chance;

                if ($params['bribeSuccess'])
                {
                    $this->navigation->addHeader('category.want');
                    $this->dispatcher->dispatch(new Other(), Other::INN_BARTENDER_BRIBE);
                    modulehook('bartenderbribe');

                    if ('' !== $this->settings->getSetting('pvp', 1) && '0' !== $this->settings->getSetting('pvp', 1))
                    {
                        $this->navigation->addNav('nav.bribe.upstairs', 'inn.php?op=bartender&act=listupstairs');
                    }
                    $this->navigation->addNav('nav.bribe.color', 'inn.php?op=bartender&act=colors');

                    if ('' !== $this->settings->getSetting('allowspecialswitch', true) && '0' !== $this->settings->getSetting('allowspecialswitch', true))
                    {
                        $this->navigation->addNav('nav.bribe.specialty', 'inn.php?op=bartender&act=specialty');
                    }
                }
                else
                {
                    $this->navigation->addNav('nav.barkeep.again', 'inn.php?op=bartender', [
                        'params' => [
                            'barkeep' => $params['barkeep'],
                        ],
                    ]);
                }
            }
        }
        elseif ('listupstairs' == $action)
        {
            $pvptime = $this->settings->getSetting('pvptimeout', 600);

            $params['paginator']  = $this->pvpListing->getPvpList($params['innName']);
            $params['sleepers']   = $this->pvpListing->getLocationSleepersCount($params['innName']);
            $params['returnLink'] = $request->getServer('REQUEST_URI');
            $params['pvpTimeOut'] = new \DateTime(date('Y-m-d H:i:s', strtotime("-{$pvptime} seconds")));
            $params['isInn']      = true;

            $this->navigation->addNav('Refresh the list', 'inn.php?op=bartender&act=listupstairs');
        }
        elseif ('colors' == $action)
        {
            $params['testText'] = (string) $request->request->get('testText');
            $params['formUrl']  = (string) $request->getServer('REQUEST_URI');

            $colors = $this->color->getColors();

            $params['colors'] = array_map(function ($n)
            {
                return "`{$n}&#96;{$n} - &#180;{$n}´{$n}";
            }, array_keys($colors));

            $params['colors'] = '<span class="ui basic small labels"><span class="ui label">'.implode('</span> <span class="ui label">', $params['colors']).'</span></span>';
        }
        elseif ('specialty' == $action)
        {
            $specialty = (string) $request->query->get('specialty');
            $uri       = (string) $request->getServer('REQUEST_URI');

            $params['specialty'] = $specialty;

            if ('' == $specialty)
            {
                $specialities = new Core();
                $this->dispatcher->dispatch($specialities, Core::SPECIALTY_NAMES);
                $specialities = modulehook('specialtynames', $specialities->getData());

                $this->navigation->addHeader('category.specialty');

                foreach ($specialities as $key => $name)
                {
                    $this->navigation->addNavNotl($name, $this->sanitize->cmdSanitize($uri."&specialty={$key}"));
                }
            }
            else
            {
                $session['user']['specialty'] = $specialty;
            }
        }
        else
        {
            $this->navigation->addHeader('category.other');
            $this->navigation->addNav('nav.return.inn', 'inn.php');

            $this->navigation->addHeader($this->sanitize->fullSanitize($params['barkeep']), [
                'translation' => false,
            ]);
            $this->navigation->addNav('Bribe', 'inn.php?op=bartender&act=bribe');

            $this->navigation->addHeader('Drinks');

            $result = new Other(['includeTemplatesPre' => $params['includeTemplatesPre'], 'includeTemplatesPost' => $params['includeTemplatesPost']]);
            $this->dispatcher->dispatch($result, Other::INN_ALE);
            $result = modulehook('ale', $result->getData());

            $params['includeTemplatesPre']  = $result['includeTemplatesPre'];
            $params['includeTemplatesPost'] = $result['includeTemplatesPost'];
        }

        return $this->renderInn($params);
    }

    public function room(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'room';

        $pay = $request->query->getInt('pay');

        $this->navigation->addHeader('category.other');
        $this->navigation->addNav('nav.return.inn', 'inn.php');

        $expense     = round(($session['user']['level'] * (10 + log($session['user']['level']))), 0);
        $fee         = $this->settings->getSetting('innfee', '5%');
        $fee         = (strpos($fee, '%')) ? round($expense * (str_replace('%', '', $fee) / 100), 0) : 0;
        $bankexpense = $expense + $fee;

        $params['fee']             = $fee;
        $params['feePercent']      = (strpos($fee, '%')) ? str_replace('%', '', $fee) / 100 : null;
        $params['expense']         = $expense;
        $params['bankExpense']     = $bankexpense;
        $params['boughtRoomToday'] = $session['user']['boughtroomtoday'];

        if (0 !== $pay)
        {
            if (2 == $pay || $session['user']['gold'] >= $expense || $params['boughtRoomToday'])
            {
                if ($session['user']['loggedin'])
                {
                    if ( ! $params['boughtRoomToday'])
                    {
                        if (2 == $pay)
                        {
                            $session['user']['goldinbank'] -= $expense;
                        }
                        else
                        {
                            $session['user']['gold'] -= $expense;
                        }

                        $session['user']['boughtroomtoday'] = 1;
                        $this->log->debug("spent {$expense} gold on an inn room");
                    }

                    $session['user']['location']    = $params['innName'];
                    $session['user']['loggedin']    = 0;
                    $session['user']['restorepage'] = 'inn.php?op=strolldown';
                    $this->tool->saveUser();
                }

                $session = [];

                return $this->redirect('home.php');
            }

            $this->addFlash('warning', $this->translator->trans('flash.message.room.not.gold', ['barkeep' => $params['barkeep']], $params['textDomain']));

            $this->navigation->addNavAllow('inn.php?op=room');

            return $this->redirect('inn.php?op=room');
        }

        if ($params['boughtRoomToday'])
        {
            $this->navigation->addNav('nav.go.room', 'inn.php?op=room&pay=1');
        }

        // $bodyguards = ['Butch', 'Bruce', 'Alfonozo', 'Guido', 'Bruno', 'Bubba', 'Al', 'Chuck', 'Brutus', 'Nunzio', 'Terrance', 'Mitch', 'Rocco', 'Spike', 'Gregor', 'Sven', 'Draco'];

        $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_INN_ROOMS);
        modulehook('innrooms');

        $this->navigation->addHeader('category.buy.room');
        $this->navigation->addNav('nav.room.buy.hand', 'inn.php?op=room&pay=1', [
            'params' => [
                'expense' => $expense,
            ],
        ]);

        if ($session['user']['goldinbank'] >= $bankexpense)
        {
            $this->navigation->addNav('nav.room.buy.bank', 'inn.php?op=room&pay=2', [
                'params' => [
                    'expense' => $bankexpense,
                ],
            ]);
        }

        return $this->renderInn($params);
    }

    public function index(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'default';

        $this->navigation->addHeader('category.do');

        $args = new GenericEvent(null, ['section' => 'inn']);
        $this->dispatcher->dispatch($args, Events::PAGE_INN_BLOCK_COMMENT_AREA);
        $args = modulehook('blockcommentarea', $args->getArguments());

        if ( ! ($args['block'] ?? false) || ! $args['block'])
        {
            $this->navigation->addNav('nav.converse', 'inn.php?op=converse');
        }
        $this->navigation->addNav('nav.barkeep.talk', 'inn.php?op=bartender', [
            'params' => [
                'barkeep' => $params['barkeep'],
            ],
        ]);

        $this->navigation->addHeader('category.other');
        $this->navigation->addNav('nav.room.get', 'inn.php?op=room');

        if ('fleedragon' == $request->query->get('op'))
        {
            --$session['user']['charm'];
            $session['user']['charm'] = max(0, $session['user']['charm']);
        }

        $chats = new GenericEvent(null, [
            [
                'chats.dragon', [], $params['textDomain'],
            ],
            [
                $this->settings->getSetting('bard', '`^Seth`0'), [], $params['textDomain'],
            ],
            [
                $this->settings->getSetting('barmaid', '`%Violet`0'), [], $params['textDomain'],
            ],
            [
                '`#MightyE`0', [], $params['textDomain'],
            ],
            [
                'chats.drink', [], $params['textDomain'],
            ],
            [
                $params['partner'], [], $params['textDomain'],
            ],
        ]);

        $this->dispatcher->dispatch($chats, Events::PAGE_INN_CHATTER);
        $chats = modulehook('innchatter', $chats->getArguments());

        $params['talk']      = $chats[array_rand($chats)];
        $params['gameclock'] = $this->dateTime->getGameTime();

        return $this->renderInn($params);
    }

    private function renderInn(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_INN_POST);
        $params = modulehook('page-inn-tpl-params', $args->getArguments());

        return $this->render('page/inn.html.twig', $params);
    }
}
