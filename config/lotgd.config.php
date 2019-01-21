<?php

return [
    \Zend\ConfigAggregator\ConfigAggregator::ENABLE_CACHE => true, //-- This cache config of service manager recomended in production
    'lotgd_core' => [
        'cache' => [
            'active' => false,
            'base_cache_dir' => 'cache',
            'config' => [
                'key_pattern' => '/^[a-z0-9_\+\-\/\.]*$/Di',
                'namespace' => 'core',
                'ttl' => 900,
                'cache_dir' => 'cache/lotgd',
            ],
        ],
        'translation' => [
            'locale' => ['en', 'en'],
            'translator_plugins' => [
                'aliases' => [
                    'Yaml' => \Lotgd\Core\Translator\Loader\Yaml::class,
                ],
                'factories' => [
                    \Lotgd\Core\Translator\Loader\Yaml::class => \Zend\ServiceManager\Factory\InvokableFactory::class
                ]
            ],
            'translation_file_patterns' => [
                [
                    'type' => 'Yaml',
                    'base_dir' => 'translations',
                    'pattern' => '%s/pages/home.yaml',
                    'text_domain' => 'home'
                ]
            ],
            'cache' => [
                'adapter' => 'filesystem',
                'ttl' => 86400,
                'options' => [
                    'cache_dir' => 'cache/translations'
                ],
                'plugins' => [
                    [
                        'name' => 'serializer',
                        'options' => []
                    ],
                    'exception_handler' => [
                        'throw_exceptions' => false
                    ],
                ]
            ]
        ]
    ]
];
