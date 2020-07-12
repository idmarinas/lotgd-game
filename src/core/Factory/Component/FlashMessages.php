<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Factory\Component;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Component\FlashMessages as CoreFlashMessages;
use Laminas\ServiceManager\{
    Factory\FactoryInterface,
    ServiceLocatorInterface
};
use Laminas\Session\SessionManager;

class FlashMessages implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $messages = new CoreFlashMessages();
        $messages->setSessionManager($container->get(SessionManager::class));

        return $messages;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
