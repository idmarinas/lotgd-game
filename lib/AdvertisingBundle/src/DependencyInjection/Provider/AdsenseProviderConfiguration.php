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

namespace LotgdCore\AdvertisingBundle\DependencyInjection\Provider;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class AdsenseProviderConfiguration implements ProviderConfigurationInterface
{
    public function buildConfiguration(NodeBuilder $node)
    {
        $node
            ->scalarNode('client')
                ->info('Publisher identificator like: ca-pub-XXXXXXX11XXX9')
            ->end()
            ->scalarNode('service_provider')
                ->defaultValue('lotgd_core_advertising.adsense')
            ->end()
            ->arrayNode('banners')
                ->isRequired()
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('style')
                            ->info('Style for block in <ins style="">')
                        ->end()
                        ->integerNode('slot')
                            ->min(0)
                            ->info('Slot ID of Ad block 8XXXXX1')
                        ->end()
                        ->enumNode('format')
                            ->info('Format of Ad, can be: auto, rectangle, vertical, horizontal')
                            ->values(['auto', 'rectangle', 'vertical', 'horizontal'])
                            ->defaultValue('auto')
                        ->end()
                        ->booleanNode('responsive')
                            ->info('Indicate if Ad is responsive, for mobile')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
