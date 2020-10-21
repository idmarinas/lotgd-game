<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Factory\Template;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollection as SymfonyEntrypointLookupCollection;

class EntrypointLookupCollection implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config      = $container->get('GameConfig');
        $entrypoints = $config['webpack_encore']['builds'] ?? [];
        $entrypoints = \count($entrypoints) ? $entrypoints : [];

        foreach ($entrypoints as $name => $path)
        {
            $builds[$name] = function () use ($path)
            {
                return new EntrypointLookup("{$path}/entrypoints.json");
            };
        }

        $collection = new ServiceManager(['factories' => $builds]);

        return new SymfonyEntrypointLookupCollection($collection);
    }
}
