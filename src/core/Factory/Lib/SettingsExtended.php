<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Lib;

use Lotgd\Core\Lib\Settings as LibSettings;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SettingsExtended implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $settings = new LibSettings();
        $settings->setWrapper($container->get(\Lotgd\Core\Db\Dbwrapper::class))
            ->setCache($container->get(\Lotgd\Core\Lib\Cache::class))
            ->setTableName('settings_extended')
            ->loadSettings()
        ;

        return $settings;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
