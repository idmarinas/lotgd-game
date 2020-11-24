<?php

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use DoctrineORMModule\Service;

//-- Added in version 4.2.0
return [
    'doctrine' => [
        'connection' => [
            // Configuration for service `doctrine.connection.orm_default` service
            'orm_default' => [
                // configuration instance to use. The retrieved service name will
                // be `doctrine.configuration.$thisSetting`
                'configuration' => 'orm_default',

                // event manager instance to use. The retrieved service name will
                // be `doctrine.eventmanager.$thisSetting`
                'eventmanager' => 'orm_default',

                // connection parameters, see
                // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                ],
            ],
        ],

        // Configuration details for the ORM.
        // See http://docs.doctrine-project.org/en/latest/reference/configuration.html
        'configuration' => [
            // Configuration for service `doctrine.configuration.orm_default` service
            'orm_default' => [
                // metadata cache instance to use. The retrieved service name will
                // be `doctrine.cache.$thisSetting`
                'metadata_cache' => 'filesystem',

                // DQL queries parsing cache instance to use. The retrieved service
                // name will be `doctrine.cache.$thisSetting`
                'query_cache' => 'filesystem',

                // ResultSet cache to use.  The retrieved service name will be
                // `doctrine.cache.$thisSetting`
                'result_cache' => 'filesystem',

                // Hydration cache to use.  The retrieved service name will be
                // `doctrine.cache.$thisSetting`
                'hydration_cache' => 'filesystem',

                // Mapping driver instance to use. Change this only if you don't want
                // to use the default chained driver. The retrieved service name will
                // be `doctrine.driver.$thisSetting`
                'driver' => 'orm_default',

                // Generate proxies automatically (turn off for production)
                'generate_proxies' => false,

                // directory where proxies will be stored. By default, this is in
                // the `data` directory of your application
                'proxy_dir' => 'storage/cache/DoctrineORMModule/Proxy',

                // namespace for generated proxy classes
                'proxy_namespace' => 'DoctrineORMModule\Proxy',

                // SQL filters. See http://docs.doctrine-project.org/en/latest/reference/filters.html
                'filters' => [],

                //-- Alias name spaces for entities
                'entity_namespaces' => [
                    'LotgdCore'  => 'Lotgd\Core\Entity',
                    'LotgdLocal' => 'Lotgd\Local\Entity',
                ],

                //-- Default EntityRepository for all Entities
                'default_repository_class_name' => \Lotgd\Core\Doctrine\ORM\EntityRepository::class,

                //-- Strategy
                'quote_strategy'  => 'Doctrine\ORM\Mapping\AnsiQuoteStrategy',
                'naming_strategy' => 'Doctrine\ORM\Mapping\UnderscoreNamingStrategy',

                // Custom DQL functions.
                // You can grab common MySQL ones at https://github.com/beberlei/DoctrineExtensions
                // Further docs at http://docs.doctrine-project.org/en/latest/cookbook/dql-user-defined-functions.html
                'datetime_functions' => [
                    'month' => \DoctrineExtensions\Query\Mysql\Month::class,
                    'year'  => \DoctrineExtensions\Query\Mysql\Year::class,
                    'date'  => \DoctrineExtensions\Query\Mysql\Date::class,
                ],
                'string_functions' => [
                    'inet_aton' => \DoctrineExtensions\Query\Mysql\InetAton::class,
                ],
                'numeric_functions' => [
                    'round' => \DoctrineExtensions\Query\Mysql\Round::class,
                    'rand'  => \DoctrineExtensions\Query\Mysql\Rand::class,
                ],

                // Second level cache configuration (see doc to learn about configuration)
                'second_level_cache' => [],
                'types' => [
                    'datetime' => \Lotgd\Core\Doctrine\DBAL\DateTimeType::class,
                ],
            ],
        ],

        // Metadata Mapping driver configuration
        'driver' => [
            'lotgd_core' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'filesystem',
                'paths' => ['src/core/Entity'],
            ],
            'lotgd_local' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'filesystem',
                'paths' => ['src/local/Entity'],
            ],

            // Configuration for service `doctrine.driver.orm_default` service
            'orm_default' => [
                // By default, the ORM module uses a driver chain. This allows multiple
                // modules to define their own entities
                'class' => MappingDriverChain::class,

                // Map of driver names to be used within this driver chain, indexed by
                // entity namespace
                'drivers' => [
                    'Lotgd\Core\Entity'  => 'lotgd_core',
                    'Lotgd\Local\Entity' => 'lotgd_local',
                ],
            ],
        ],
        // Entity Manager instantiation settings
        'entitymanager' => [
            // configuration for the `doctrine.entitymanager.orm_default` service
            'orm_default' => [
                // connection instance to use. The retrieved service name will
                // be `doctrine.connection.$thisSetting`
                'connection' => 'orm_default',

                // configuration instance to use. The retrieved service name will
                // be `doctrine.configuration.$thisSetting`
                'configuration' => 'orm_default',
            ],
        ],
        'eventmanager' => [
            // configuration for the `doctrine.eventmanager.orm_default` service
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\Translatable\TranslatableListener',
                    // 'Gedmo\Tree\TreeListener',
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                    'Gedmo\Loggable\LoggableListener',
                    'Gedmo\Sortable\SortableListener',
                    'Lotgd\Core\Doctrine\Extension\TablePrefix',
                ],
            ],
        ],

        // SQL logger collector, used when Laminas\DeveloperTools and its toolbar are active
        'sql_logger_collector' => [
            // configuration for the `doctrine.sql_logger_collector.orm_default` service
            'orm_default' => [],
        ],

        // mappings collector, used when Laminas\DeveloperTools and its toolbar are active
        'mapping_collector' => [
            // configuration for the `doctrine.sql_logger_collector.orm_default` service
            'orm_default' => [],
        ],

        // form annotation builder configuration
        'formannotationbuilder' => [
            // Configuration for service `doctrine.formannotationbuilder.orm_default` service
            'orm_default' => [],
        ],

        // entity resolver configuration, allows mapping associations to interfaces
        'entity_resolver' => [
            // configuration for the `doctrine.entity_resolver.orm_default` service
            'orm_default' => [],
        ],

        // authentication service configuration
        'authentication' => [
            // configuration for the `doctrine.authentication.orm_default` authentication service
            'orm_default' => [
                // name of the object manager to use. By default, the EntityManager is used
                'objectManager' => 'doctrine.entitymanager.orm_default',
                //'identityClass' => 'Application\Model\User',
                //'identityProperty' => 'username',
                //'credentialProperty' => 'password',
            ],
        ],

        'authenticationadapter' => [
            'odm_default' => true,
            'orm_default' => true,
        ],
        'authenticationstorage' => [
            'odm_default' => true,
            'orm_default' => true,
        ],
        'authenticationservice' => [
            'odm_default' => true,
            'orm_default' => true,
        ],

        // migrations configuration
        'migrations_configuration' => [
            'orm_default' => [
                'directory'       => 'data/DoctrineORMModule/Migrations',
                'name'            => 'Doctrine Database Migrations',
                'namespace'       => 'DoctrineORMModule\Migrations',
                'table'           => 'migrations',
                'column'          => 'version',
                'custom_template' => null,
            ],
        ],

        // migrations commands base config
        'migrations_cmd' => [
            'generate' => [],
            'execute'  => [],
            'migrate'  => [],
            'status'   => [],
            'version'  => [],
            'diff'     => [],
            'latest'   => [],
        ],

        'cache' => [
            'apc' => [
                'class'     => 'Doctrine\Common\Cache\ApcCache',
                'namespace' => 'DoctrineModule',
            ],
            'apcu' => [
                'class'     => 'Doctrine\Common\Cache\ApcuCache',
                'namespace' => 'DoctrineModule',
            ],
            'array' => [
                'class'     => 'Doctrine\Common\Cache\ArrayCache',
                'namespace' => 'DoctrineModule',
            ],
            'filesystem' => [
                'class'     => 'Doctrine\Common\Cache\FilesystemCache',
                'directory' => 'storage/cache/DoctrineModule/cache',
                'namespace' => 'DoctrineModule',
            ],
            'memcache' => [
                'class'     => 'Doctrine\Common\Cache\MemcacheCache',
                'instance'  => 'my_memcache_alias',
                'namespace' => 'DoctrineModule',
            ],
            'memcached' => [
                'class'     => 'Doctrine\Common\Cache\MemcachedCache',
                'instance'  => 'my_memcached_alias',
                'namespace' => 'DoctrineModule',
            ],
            'predis' => [
                'class'     => 'Doctrine\Common\Cache\PredisCache',
                'instance'  => 'my_predis_alias',
                'namespace' => 'DoctrineModule',
            ],
            'redis' => [
                'class'     => 'Doctrine\Common\Cache\RedisCache',
                'instance'  => 'my_redis_alias',
                'namespace' => 'DoctrineModule',
            ],
            'wincache' => [
                'class'     => 'Doctrine\Common\Cache\WinCacheCache',
                'namespace' => 'DoctrineModule',
            ],
            'xcache' => [
                'class'     => 'Doctrine\Common\Cache\XcacheCache',
                'namespace' => 'DoctrineModule',
            ],
            'zenddata' => [
                'class'     => 'Doctrine\Common\Cache\ZendDataCache',
                'namespace' => 'DoctrineModule',
            ],
        ],
    ],

    // Factory mappings - used to define which factory to use to instantiate a particular doctrine
    // service type
    'doctrine_factories' => [
        'cache'                    => 'DoctrineModule\Service\CacheFactory',
        'eventmanager'             => 'DoctrineModule\Service\EventManagerFactory',
        'driver'                   => 'DoctrineModule\Service\DriverFactory',
        'authenticationadapter'    => 'DoctrineModule\Service\Authentication\AdapterFactory',
        'authenticationstorage'    => 'DoctrineModule\Service\Authentication\StorageFactory',
        'authenticationservice'    => 'DoctrineModule\Service\Authentication\AuthenticationServiceFactory',
        'connection'               => Service\DBALConnectionFactory::class,
        'configuration'            => Service\ConfigurationFactory::class,
        'entitymanager'            => \Lotgd\Core\Factory\Db\Doctrine::class,
        'entity_resolver'          => Service\EntityResolverFactory::class,
        'sql_logger_collector'     => Service\SQLLoggerCollectorFactory::class,
        'mapping_collector'        => Service\MappingCollectorFactory::class,
        'formannotationbuilder'    => Service\FormAnnotationBuilderFactory::class,
        'migrations_configuration' => Service\MigrationsConfigurationFactory::class,
        'migrations_cmd'           => Service\MigrationsCommandFactory::class,
    ],
];
