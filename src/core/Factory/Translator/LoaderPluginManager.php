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

namespace Lotgd\Core\Factory\Translator;

use Interop\Container\ContainerInterface;
use Zend\I18n\Translator\LoaderPluginManager as ZendLoaderPluginManager;
use Zend\ServiceManager\{
    Config,
    FactoryInterface,
    ServiceLocatorInterface
};

class LoaderPluginManager implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $options = $options ?: [];
        $pluginManager = new ZendLoaderPluginManager($container, $options);

        // If we do not have a GameConfig service, nothing more to do
        if (! $container->has('GameConfig'))
        {
            return $pluginManager;
        }

        $config = $container->get('GameConfig')['lotgd_core'] ?? [];

        // If we do not have translator_plugins configuration, nothing more to do
        if (! isset($config['translation']['translator_plugins']) || ! is_array($config['translation']['translator_plugins']))
        {
            return $pluginManager;
        }

        (new Config($config['translation']['translator_plugins']))->configureServiceManager($pluginManager);

        return $pluginManager;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
