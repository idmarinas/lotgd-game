<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void
{
    $container->extension('stof_doctrine_extensions', [
        'default_locale' => '%kernel.default_locale%',
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
