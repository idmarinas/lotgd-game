<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\AvatarBundle\DependencyInjection;

use Lotgd\Bundle\AvatarBundle\Form\CreateAvatar;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('lotgd_avatar');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('form')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('create_avatar')
                            ->cannotBeEmpty()
                            ->defaultValue(CreateAvatar::class)
                            ->info('Form for creating a new avatar (character)')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('avatar')->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('max_per_user')
                            ->defaultValue(1)
                            ->min(1)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
