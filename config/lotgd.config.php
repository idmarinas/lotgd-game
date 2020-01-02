<?php

return [
    \Zend\ConfigAggregator\ConfigAggregator::ENABLE_CACHE => true, //-- This cache config of service manager recomended in production
    'lotgd_core' => [
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
