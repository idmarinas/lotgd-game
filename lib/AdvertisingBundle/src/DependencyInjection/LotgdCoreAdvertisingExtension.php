<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.11.0
 */

namespace LotgdCore\AdvertisingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class LotgdCoreAdvertisingExtension extends ConfigurableExtension
{
    private const SUPPORTED_PROVIDER_TYPES = [
        'adsense' => Provider\AdsenseProviderConfiguration::class,
        'generic' => Provider\GenericProviderConfiguration::class,
    ];
    protected $configurators = [];

    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));

        $loader->load('services.php');

        $providers = $mergedConfig['providers'];

        //-- Configure all prividers
        foreach ($providers as $name => $providerConfig)
        {
            if ( ! isset($providerConfig['type']))
            {
                throw new InvalidConfigurationException(\sprintf('Your "lotgd_core_advertising.providers.%s" config entry is missing the "type" key.', $name));
            }

            $provider = $providerConfig['type'];
            unset($providerConfig['type']);

            if ( ! isset(self::SUPPORTED_PROVIDER_TYPES[$provider]))
            {
                throw new InvalidConfigurationException(\sprintf('The "lotgd_core_advertising.providers" config "type" key "%s" is not supported. We support (%s)', $provider, \implode(', ', self::SUPPORTED_PROVIDER_TYPES)));
            }

            //-- Process configuration
            $tree = new TreeBuilder('lotgd_core_advertising/providers/'.$name);
            $node = $tree->getRootNode();

            $this->buildConfigurationForProviderType($node, $provider);

            $processor = new Processor();
            $config    = $processor->process($tree->buildTree(), [$providerConfig]);

            $this->configureProviderAndService($container, $config, $mergedConfig['enable']);
        }
    }

    /**
     * @param string $type
     * @param mixed  $provider
     *
     * @return ProviderConfiguratorInterface
     */
    public function getConfigurator($provider)
    {
        if ( ! isset($this->configurators[$provider]))
        {
            $class = self::SUPPORTED_PROVIDER_TYPES[$provider];

            $this->configurators[$provider] = new $class();
        }

        return $this->configurators[$provider];
    }

    private function configureProviderAndService(ContainerBuilder $container, array $config, bool $advertisingEnable)
    {
        $definition = $container->getDefinition($config['service_provider']);
        $definition->addMethodCall('configure', [$config]);

        $definition->addMethodCall('disableAdvertising'); //-- Disabled by default

        if ($advertisingEnable)
        {
            $definition->addMethodCall('enableAdvertising');
        }
    }

    private function buildConfigurationForProviderType(NodeDefinition $node, $provider)
    {
        $optionsNode = $node->children();

        $optionsNode
            ->booleanNode('enable')
            ->defaultFalse()
            ->end()
        ;

        // allow the specific provider to add more options
        $this->getConfigurator($provider)
            ->buildConfiguration($optionsNode)
        ;

        $optionsNode->end();
    }
}
