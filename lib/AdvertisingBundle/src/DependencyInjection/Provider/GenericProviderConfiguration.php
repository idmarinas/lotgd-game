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

class GenericProviderConfiguration implements ProviderConfigurationInterface
{
    public function buildConfiguration(NodeBuilder $node)
    {
        $node
            ->scalarNode('service_provider')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->arrayNode('banners')
                ->isRequired()
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;

    }
}
