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

return static function (ContainerConfigurator $container): void
{
    $container->extension('stof_doctrine_extensions', [
        'translation_fallback' => true,
        'orm'            => [
            'default' => [
                'translatable'        => true,
                'timestampable'       => true,
                'blameable'           => false,
                'sluggable'           => true,
                'tree'                => false,
                'loggable'            => true,
                'sortable'            => true,
                'softdeleteable'      => true,
                'uploadable'          => false,
                'reference_integrity' => false,
            ],
        ],
    ]);
};
