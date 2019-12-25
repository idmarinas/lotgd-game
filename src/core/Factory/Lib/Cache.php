<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Lib;

use Lotgd\Core\Lib\Cache as LibCache;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Cache implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('GameConfig');
        $options = $options['lotgd_core']['cache'] ?? [];
        $config = $options['config'] ?? [];
        $config = is_array($config) ? $config : [];

        $cache = new LibCache($config);
        $cache->setActive((bool) ($options['active'] ?? false));

        return $cache;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
