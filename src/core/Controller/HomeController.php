<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Kernel;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    private $settings;
    private $dispatcher;
    private $translator;
    private $dateTime;
    private $navigation;

    public function __construct(
        Settings $settings,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        DateTime $dateTime,
        Navigation $navigation
    ) {
        $this->settings   = $settings;
        $this->dispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->dateTime   = $dateTime;
        $this->navigation = $navigation;
    }

    public function index(Request $request): Response
    {
        $this->navigation->addHeader('home.category.new');
        $this->navigation->addNav('home.nav.create', 'create.php');

        $this->navigation->addHeader('home.category.func');
        $this->navigation->addNav('home.nav.forgot', 'create.php?op=forgot');
        $this->navigation->addNav('home.nav.list', 'list.php');
        $this->navigation->addNav('home.nav.news', 'news.php');

        $this->navigation->addHeader('home.category.other');
        $this->navigation->addNav('home.nav.about', 'about.php');
        $this->navigation->addNav('home.nav.setup', 'about.php?op=setup');
        $this->navigation->addNav('home.nav.net', 'logdnet.php?op=list');
        //-- Parameters to be passed to the template
        $params = [
            'villagename'           => $this->settings->getSetting('villagename', LOCATION_FIELDS),
            'includeTemplatesPre'   => [], //-- Templates that are in top of content (but below of title)
            'includeTemplatesIndex' => [], //-- Templates that are in index below of new player
            'includeTemplatesPost'  => [], //-- Templates that are in bottom of content
            'gameclock'             => '' !== $this->settings->getSetting('homecurtime', 1) && '0' !== $this->settings->getSetting('homecurtime', 1) ? $this->dateTime->getGameTime() : null,
            'newdaytimer'           => '' !== $this->settings->getSetting('homenewdaytime', 1) && '0' !== $this->settings->getSetting('homenewdaytime', 1) ? $this->dateTime->secondsToNextGameDay() : null,
        ];

        //-- Get newest player name if show in home page
        if ('' !== $this->settings->getSetting('homenewestplayer', 1) && '0' !== $this->settings->getSetting('homenewestplayer', 1))
        {
            $name = $this->settings->getSetting('newestPlayerName', '');
            $old  = (int) $this->settings->getSetting('newestPlayerOld', 0);
            $new  = (int) $this->settings->getSetting('newestplayer', 0);

            if (0 != $new && $old !== $new)
            {
                /** @var Lotgd\Core\Repository\CharactersRepository $character */
                $character = $this->getDoctrine()->getRepository('LotgdCore:Avatar');
                $name      = $character->getCharacterNameFromAcctId($new);
                $this->settings->saveSetting('newestPlayerName', $name);
                $this->settings->saveSetting('newestPlayerOld', $new);
            }

            $params['newestplayer'] = $name;
        }

        if (abs($this->settings->getSetting('OnlineCountLast', 0) - strtotime('now')) > 60)
        {
            /** @var \Lotgd\Core\Repository\UserRepository $account */
            $account = $this->getDoctrine()->getRepository('LotgdCore:User');

            $this->settings->saveSetting('OnlineCount', $account->getCountAcctsOnline((int) $this->settings->getSetting('LOGINTIMEOUT', 900)));
            $this->settings->saveSetting('OnlineCountLast', strtotime('now'));
        }

        $params['OnlineCount'] = $this->settings->getSetting('OnlineCount', 0);

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_HOME_TEXT);
        $results = $args->getArguments();

        if (\is_array($results) && \count($results))
        {
            $params['hookHomeText'] = $results;
        }

        if ( ! $request->getCookie('lgi'))
        {
            $this->addFlash('warning', $this->translator->trans('session.cookies.unactive', [], 'app_default'));
            $this->addFlash('info', $this->translator->trans('session.cookies.info', [], 'app_default'));
        }

        $params['serverFull'] = true;

        if ($params['OnlineCount'] < $this->settings->getSetting('maxonline', 0) || 0 == $this->settings->getSetting('maxonline', 0))
        {
            $params['serverFull'] = false;
        }

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_HOME_MIDDLE);
        $results = $args->getArguments();

        if (\is_array($results) && \count($results))
        {
            $params['hookHomeMiddle'] = $results;
        }

        //-- By default not have banner are
        $params['loginBanner'] = $this->settings->getSetting('loginbanner');
        //-- Version of the game the server is running
        $params['serverVersion'] = Kernel::VERSION;

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_HOME_POST);
        $params = $args->getArguments();

        return $this->render('page/home.html.twig', $params);
    }
}
