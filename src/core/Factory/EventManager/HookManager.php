<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Factory\EventManager;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Lotgd\Core\EventManagerAware;

class HookManager implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $eventManager = new EventManagerAware();

        return $eventManager->getEventManager();
    }
}
