<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\DependencyInjection\Compiler;

use Lotgd\Core\Bag\NotificationsBag;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NotificationsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ( ! $container->has(NotificationsBag::class) || ! $container->hasDefinition('session'))
        {
            return;
        }

        $container->getDefinition('session')
            ->addMethodCall('registerBag', [new Reference(NotificationsBag::class)])
        ;
    }
}
