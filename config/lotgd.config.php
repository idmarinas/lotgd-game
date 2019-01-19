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
            'locale' => 'en',
            'translation_file_patterns' => [
                [
                    'type' => 'php',
                    'base_dir' => 'translations',
                    'pattern' => '%s/pages/home.php',
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
