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

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Lib\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    private $settings;
    private $dispatcher;
    private $doctrine;
    private $translator;

    public function __construct(Settings $settings, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $doctrine, TranslatorInterface $translator)
    {
        $this->settings   = $settings;
        $this->dispatcher = $eventDispatcher;
        $this->doctrine   = $doctrine;
        $this->translator = $translator;
    }

    public function index(Request $request): Response
    {
        $params = [];
        //-- Parameters to be passed to the template
        $params = [
            'villagename'           => $this->settings->getSetting('villagename', LOCATION_FIELDS),
            'includeTemplatesPre'   => [], //-- Templates that are in top of content (but below of title)
            'includeTemplatesIndex' => [], //-- Templates that are in index below of new player
            'includeTemplatesPost'  => [], //-- Templates that are in bottom of content
            'gameclock'             => $this->settings->getSetting('homecurtime', 1) ? getgametime() : null,
            'newdaytimer'           => $this->settings->getSetting('homenewdaytime', 1) ? secondstonextgameday() : null,
        ];

        //-- Get newest player name if show in home page
        if ($this->settings->getSetting('homenewestplayer', 1))
        {
            $name = (string) $this->settings->getSetting('newestPlayerName', '');
            $old  = (int) $this->settings->getSetting('newestPlayerOld', 0);
            $new  = (int) $this->settings->getSetting('newestplayer', 0);

            if (0 != $new && $old != $new)
            {
                /** @var Lotgd\Core\EntityRepository\CharactersRepository */
                $character = $this->doctrine->getRepository('LotgdCore:Characters');
                $name      = $character->getCharacterNameFromAcctId($new);
                $this->settings->saveSetting('newestPlayerName', $name);
                $this->settings->saveSetting('newestPlayerOld', $new);
            }

            $params['newestplayer'] = $name;
        }

        if (\abs($this->settings->getSetting('OnlineCountLast', 0) - \strtotime('now')) > 60)
        {
            /** @var \Lotgd\Core\EntityRepository\AccountsRepository */
            $account = $this->doctrine->getRepository('LotgdCore:Accounts');

            $this->settings->saveSetting('OnlineCount', $account->getCountAcctsOnline((int) $this->settings->getSetting('LOGINTIMEOUT', 900)));
            $this->settings->saveSetting('OnlineCountLast', \strtotime('now'));
        }

        $params['OnlineCount'] = $this->settings->getSetting('OnlineCount', 0);

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_HOME_TEXT);
        $results = modulehook('hometext', $args->getArguments());

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
        $results = modulehook('homemiddle', $args->getArguments());

        if (\is_array($results) && \count($results))
        {
            $params['hookHomeMiddle'] = $results;
        }

        //-- By default not have banner are
        $params['loginBanner'] = $this->settings->getSetting('loginbanner');
        //-- Version of the game the server is running
        $params['serverVersion'] = \Lotgd\Core\Kernel::VERSION;

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_HOME_POST);
        $params = modulehook('page-home-tpl-params', $args->getArguments());

        return $this->render('page/home.html.twig', $params);
    }
}
