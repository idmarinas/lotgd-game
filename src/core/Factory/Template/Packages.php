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
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;

class Packages implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config      = $container->get('GameConfig');
        $entrypoints = $config['webpack_encore']['builds'] ?? [];
        $entrypoints = \count($entrypoints) ? $entrypoints : ['_default' => 'public/build'];

        //-- this is the default not overwrite
        $builds = ['lotgd' => function ()
        {
            return new EntrypointLookup('public/build/lotgd/entrypoints.json');
        }, ];
        $packages = ['lotgd' => new Package(new JsonManifestVersionStrategy('public/build/lotgd/manifest.json'))];

        foreach ($entrypoints as $name => $path)
        {
            $packages[$name] = new Package(new JsonManifestVersionStrategy("{$path}/manifest.json"));
            $builds[$name]   = function () use ($path)
            {
                return new EntrypointLookup("{$path}/entrypoints.json");
            };
        }

        $defaultPackage = new Package(new JsonManifestVersionStrategy('public/build/lotgd/manifest.json'));

        $packages = new CorePackages($defaultPackage, $packages);

        return [
            'packages'    => $packages,
            'collections' => $builds,
        ];
    }
}
