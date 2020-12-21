<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Lib;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Lotgd\Core\Lib\Settings as LibSettings;

class Settings implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $settings = new LibSettings();
        $settings->setDoctrine(\LotgdKernel::get('doctrine.orm.entity_manager'))
            ->setCache(\LotgdKernel::get('core.settings.cache'))
            ->loadSettings()
        ;

        return $settings;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
