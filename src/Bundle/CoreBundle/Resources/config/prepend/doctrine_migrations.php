<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('doctrine_migrations', [
        'migrations_paths' => [
            'Lotgd\Bundle\CoreBundle\Migration' => '@LotgdCoreBundle/Migration',
        ],
        //-- Possible values: "BY_YEAR", "BY_YEAR_AND_MONTH", false
        'organize_migrations' => 'BY_YEAR',
        'storage' => [
            'table_storage' => [
                'table_name' => 'lotgd_migration_versions'
            ]
        ]
    ]);
};
