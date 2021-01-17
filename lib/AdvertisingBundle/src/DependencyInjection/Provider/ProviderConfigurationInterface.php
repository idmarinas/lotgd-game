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

interface ProviderConfigurationInterface
{
    /**
     * Build configuration for provider.
     */
    public function buildConfiguration(NodeBuilder $node);
}
