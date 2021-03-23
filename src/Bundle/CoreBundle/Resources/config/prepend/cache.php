<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'cache' => [
            'prefix_seed' => 'lotgd_core/package',
            'pools' => [
                'lotgd.core.package.cache' => [
                    'adapter' => 'cache.app',
                    'tags' => true
                ]
            ]
        ]
    ]);
};
