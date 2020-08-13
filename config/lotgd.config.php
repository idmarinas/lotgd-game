<?php

return [
    \Laminas\ConfigAggregator\ConfigAggregator::ENABLE_CACHE => true, //-- This cache config of service manager recomended in production
    'lotgd_core' => [
        'translation' => [
            'locale'             => ['language' => 'en', 'fallbackLanguage' => 'en'],
            'translator_plugins' => [
                'aliases' => [
                    'Yaml' => \Lotgd\Core\Translator\Loader\Yaml::class,
                ],
                'factories' => [
                    \Lotgd\Core\Translator\Loader\Yaml::class => \Laminas\ServiceManager\Factory\InvokableFactory::class,
                ],
            ],
        ],
    ],
];
