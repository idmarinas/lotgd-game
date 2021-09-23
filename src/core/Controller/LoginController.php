<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Event\Core;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Tool\Tool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    private $dispatcher;
    private $translator;
    private $tools;
    private $settings;
    private $cache;
    private $sessionHttp;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        Tool $tools,
        Settings $settings,
        CacheInterface $cacheApp,
        SessionInterface $session
    ) {
        $this->dispatcher  = $eventDispatcher;
        $this->translator  = $translator;
        $this->tools       = $tools;
        $this->settings    = $settings;
        $this->cache       = $cacheApp;
        $this->sessionHttp = $session;
    }

    public function logout()
    {
        global $session;

        if ($session['user']['loggedin'])
        {
            $iname = $this->settings->getSetting('innname', LOCATION_INN);

            $session['user']['restorepage'] = 'news.php';
            if (($session['user']['superuser'] & (0xFFFFFFFF & ~SU_DOESNT_GIVE_GROTTO)) !== 0)
            {
                $session['user']['restorepage'] = 'superuser.php';
            }
            elseif ($session['user']['location'] == $iname)
            {
                $session['user']['restorepage'] = 'inn.php?op=strolldown';
            }

            $session['user']['loggedin'] = false;

            $this->cache->delete('char-list-home-page');

            // Let's throw a logout module hook in here so that modules
            // like the stafflist which need to invalidate the cache
            // when someone logs in or off can do so.
            $this->dispatcher->dispatch(new Core(), Core::LOGOUT_PLAYER);
            modulehook('player-logout');
            $this->tools->saveUser();

            $this->sessionHttp->invalidate();

            $this->addFlash('info', $this->translator->trans('logout.success', [], 'page_login'));
        }

        $session = [];

        return $this->redirect('index.php');
    }
}
