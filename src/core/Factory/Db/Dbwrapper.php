<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Db;

use Interop\Container\ContainerInterface;
use Lotgd\Core\Db\Dbwrapper as LibDbwrapper;
use Zend\ServiceManager\{
    FactoryInterface,
    ServiceLocatorInterface
};
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
        $wrapper->setContainer($container);

        return $wrapper;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
