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
    $container->extension('sonata_admin', [
        'assets' => [
            'extra_stylesheets' => [
                'bundles/sonatatranslation/css/sonata-translation.css',
            ],
        ],
    ]);

    $container->extension('sonata_block', [
        'blocks' => [
            'sonata_translation.block.locale_switcher' => null,
        ],
    ]);

    $container->extension('sonata_translation', [
        'locales'             => ['en'],
        'default_locale'      => '%kernel.default_locale%',
        'default_filter_mode' => 'gedmo',
        'locale_switcher'     => true,
        'gedmo'               => [
            'enabled' => true,
        ],
    ]);
};
