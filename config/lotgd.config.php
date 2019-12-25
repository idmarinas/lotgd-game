<?php

return [
    \Zend\ConfigAggregator\ConfigAggregator::ENABLE_CACHE => true, //-- This cache config of service manager recomended in production
    'lotgd_core' => [
        'cache' => [
            'active' => false, //-- Change to TRUE for activate cache in core
            'base_cache_dir' => 'storage/cache',
            'config' => [
                'key_pattern' => '/^[a-z0-9_\+\-\/\.]*$/Di',
                'namespace' => 'core',
                'ttl' => 900,
                'cache_dir' => 'storage/cache/lotgd',
            ],
        ],
        'translation' => [
            'locale' => [ 'language' => 'en', 'fallbackLanguage' => 'en' ],
            'translator_plugins' => [
                'aliases' => [
                    'Yaml' => \Lotgd\Core\Translator\Loader\Yaml::class,
                ],
                'factories' => [
                    \Lotgd\Core\Translator\Loader\Yaml::class => \Zend\ServiceManager\Factory\InvokableFactory::class
                ]
            ],
            'cache' => [
                'adapter' => 'filesystem',
                'ttl' => 86400,
                'options' => [
                    'cache_dir' => 'storage/cache/translation'
                ],
                'plugins' => [
                    'serializer',
                    'optimize_by_factor',
                    'clear_expired_by_factor',
                    'exception_handler' => [
                        'throw_exceptions' => false
                    ]
                ]
            ]
        ]
    ]
];
