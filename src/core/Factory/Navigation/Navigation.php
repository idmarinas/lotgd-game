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

namespace Lotgd\Core\Factory\Navigation;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Navigation\Navigation as NavigationCore;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Navigation implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('GameConfig');
        $options = $config['lotgd_core'] ?? [];
        $classes = $options['navigation']['classes'] ?? [];

        $navigation = new NavigationCore();

        if (!empty($classes) && is_array($classes))
        {
            $navigation->setClassHeader($classes['header'] ?: 'navhead')
                ->setClassNav($classes['nav'] ?: 'nav')
            ;
        }

        return $navigation;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
