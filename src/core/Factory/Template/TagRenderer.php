<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Factory\Template;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer as SymfonyTagRenderer;

class TagRenderer implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $packages   = $container->get('webpack_encore.packages');
        $entrypoint = $container->get('webpack_encore.entrypoint_lookup_collection');

        return new SymfonyTagRenderer($entrypoint, $packages);
    }
}
