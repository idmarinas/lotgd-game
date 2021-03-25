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
