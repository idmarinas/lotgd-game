<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Factory\Lib;

use Lotgd\Core\Lib\Settings as LibSettings;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Settings implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $settings = new LibSettings();
        $settings->setDoctrine($container->get(\Lotgd\Core\Db\Doctrine::class))
            ->setCache($container->get(\Lotgd\Core\Lib\Cache::class))
            ->loadSettings()
        ;

        return $settings;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
