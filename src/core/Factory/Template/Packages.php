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
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages as CorePackages;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class Packages implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config      = $container->get('GameConfig');
        $entrypoints = $config['webpack_encore']['builds'] ?? [];
        $entrypoints = \count($entrypoints) ? $entrypoints : [];

        $packages = [];

        foreach ($entrypoints as $name => $path)
        {
            $packages[$name] = new Package(new JsonManifestVersionStrategy("{$path}/manifest.json"));
        }

        return new CorePackages(null, $packages);
    }
}
