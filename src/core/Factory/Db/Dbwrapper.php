<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Db;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Lotgd\Core\Db\Dbwrapper as LibDbwrapper;

\trigger_error(\sprintf(
    'Usage of Factory %s is deprecated, please use Doctrine of LoTGD Kernel instead. "$doctrine = LotgdKernel::get("doctrine.orm.entity_manager")" or "Doctrine::" static class',
    Dbwrapper::class
), E_USER_DEPRECATED);

/**
 * @deprecated 4.4.0
 */
class Dbwrapper implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $options = $container->get('GameConfig');
        $options = $options['lotgd_core']['db'] ?? [];
        $adapter = $options['adapter']          ?? [];
        $adapter = \is_array($adapter) ? $adapter : [];

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
