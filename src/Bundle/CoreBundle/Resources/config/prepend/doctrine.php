<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use DoctrineExtensions\Query\Mysql\Date;
use Lotgd\Bundle\CoreBundle\Doctrine\DBAL\DateTimeType;
use DoctrineExtensions\Query\Mysql\InetAton;
use DoctrineExtensions\Query\Mysql\Month;
use DoctrineExtensions\Query\Mysql\Rand;
use DoctrineExtensions\Query\Mysql\Round;
use DoctrineExtensions\Query\Mysql\Year;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;

return static function (ContainerConfigurator $container): void
{
    $container->extension('doctrine', [
        'dbal' => [
            'charset'       => 'utf8',
            'mapping_types' => [
                'enum' => 'string',
            ],
            'types' => [
                'datetime' => DateTimeType::class,
            ],
            'default_table_options' => [
                'charset' => 'utf8',
                'collate' => 'utf8_unicode_ci'
            ]
        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'quote_strategy' => 'doctrine.orm.quote_strategy.ansi',
            'mappings' => [
                'LotgdCoreBundle' => [
                    'is_bundle' => true,
                    'type' => 'annotation',
                    'prefix' => 'Lotgd\Bundle\CoreBundle\Entity',
                    'alias' => 'LotgdCore'
                ]
            ],
            'dql' => [
                'string_functions' => [
                    'inet_aton' => InetAton::class
                ],
                'numeric_functions' => [
                    'round' => Round::class,
                    'rand' => Rand::class,
                ],
                'datetime_functions' => [
                    'month' => Month::class,
                    'year' => Year::class,
                    'date' => Date::class
                ]
            ],
            'filters' => [
                'softdeleteable' => [
                    'class' => SoftDeleteableFilter::class,
                    'enabled' => true
                ]
            ]
        ]
    ]);
};
