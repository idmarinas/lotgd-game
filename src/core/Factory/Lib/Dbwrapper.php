<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Lib;

use Lotgd\Core\Lib\Dbwrapper as LibDbwrapper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Dbwrapper implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('GameConfig');
        $options = $options['lotgd_core']['db'] ?? [];
        $adapter = $options['adapter'] ?? [];
        $adapter = is_array($adapter) ? $adapter : [];

        $wrapper = new LibDbwrapper($adapter);
        $wrapper->setPrefix($options['prefix'] ?? '');

        return $wrapper;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
