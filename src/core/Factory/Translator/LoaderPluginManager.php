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
use Lotgd\Core\Exception;
use Zend\I18n\Translator\LoaderPluginManager as ZendLoaderPluginManager;
use Zend\ServiceManager\{
    Config,
    Factory\FactoryInterface,
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

        $translatorPlugins = $config['translation']['translator_plugins'] ?? [];
        //-- If not find config for plugins throw exception
        if (empty($translatorPlugins))
        {
            throw new Exception\ConfigNotFound('The "translator_plugins" configuration is missing in the "translation" configuration matrix.');
        }

        (new Config($translatorPlugins))->configureServiceManager($pluginManager);

        return $pluginManager;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
