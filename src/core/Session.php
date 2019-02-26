<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core;

use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Validator;

class Session
{
    use Pattern\Container;

    /**
     * Init session of app.
     *
     * @param bool|null $force
     */
    public function bootstrapSession(?bool $force = null)
    {
        $session = $this->getContainer(SessionManager::class);
        $session->start();

        $container = new Container('initialized');

        if (isset($container->init) && ! $force)
        {
            return;
        }

        $request = $this->getContainer(Http::class);

        $session->regenerateId(true);
        $container->init = 1;
        $container->remoteAddr = $request->getServer()->get('REMOTE_ADDR');
        $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
        $config = $this->getContainer('GameConfig');

        if (! isset($config['session_manager']))
        {
            return;
        }

        $sessionConfig = $config['session_manager'];

        if (! isset($sessionConfig['validators']))
        {
            return;
        }

        $chain = $session->getValidatorChain();

        foreach ($sessionConfig['validators'] as $validator)
        {
            switch ($validator)
            {
                case Validator\HttpUserAgent::class:
                    $validator = new $validator($container->httpUserAgent);
                break;
                case Validator\RemoteAddr::class:
                    $validator = new $validator($container->remoteAddr);
                break;
                default:
                    $validator = new $validator();
                break;
            }

            $chain->attach('session.validate', [$validator, 'isValid']);
        }
    }

    /**
     * Logout of app.
     */
    public function sessionLogOut()
    {
        try
        {
            $session = $this->getContainer(SessionManager::class);
            $container = new Container('initialized');

            $session->forgetMe();

            session_destroy();

            $session->regenerateId(true);
            $session->getStorage()->clear();
            $container->init = 1;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return redirect('home.php');
        }
    }
}
