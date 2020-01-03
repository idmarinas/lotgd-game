<?php

/**
 * IMPORTANT!!!!!
 *
 * You can create your own cache system (or several).
 *
 * Please do not modify any of these caches, you may alter the behavior of the cache and fail to comply with your purpose.
 * Modify this cache may prompt you not to return the correct data.
 *
 * It's best to create your own cache by following these examples.
 */

return [
    'caches' => [
        //-- This is a main cache of Game, please not modify
        'Cache\Core\Lotgd' => [
            'title' => 'LoTGD Core', //-- This is a title of cache
            'description' => 'Cache of LoTGD Core, this is the main cache of game.', //-- This describe purpose of cache
            'adapter' => 'filesystem',
			'options' => [
                'ttl' => 900,
                'key_pattern' => '/^[a-z0-9_\+\-\/\.]*$/Di',
				'cache_dir' => 'storage/cache/lotgd',
                'namespace' => 'core'
			],
            'plugins' => [
				'serializer',
                'exception_handler' => [
                    'throw_exceptions' => false
                ],
            ]
        ],
        //-- This cache is exclusive to the cronjob
        'Cache\Core\Cronjob' => [
            'title' => 'LoTGD CronjJob',
            'description' => 'Cache of CronJob, this is exclusive of cronjob, is use to cache all available cronjobs.',
            'adapter' => 'filesystem',
			'options' => [
                'ttl' => 900,
                'key_pattern' => '/^[a-z0-9_\+\-\/\.]*$/Di',
                'cache_dir' => 'storage/cache/cronjob',
                'dir_permission' => 0777, //-- For avoid problems when optimize cache
                'namespace' => 'cronjob'
			],
            'plugins' => [
                'serializer',
                'exception_handler' => [
                    'throw_exceptions' => false
                ],
            ]
        ],
        //-- This cache is exclusive for translations. NOT use for other purpose.
        'Cache\Core\Translator' => [
            'title' => 'LoTGD Translator',
            'description' => 'Cache of Translations, this cache is used exclusive for translation. This cache is automatically optimized and cleaned. If you change any translation clear all cache.',
            'adapter' => 'filesystem',
            'options' => [
                'ttl' => 86400,
                'cache_dir' => 'storage/cache/translation'
            ],
            'plugins' => [
                'serializer',
                'optimize_by_factor' => [
                    'optimizing_factor' => 200
                ],
                'clear_expired_by_factor' => [
                    'clearing_factor' => 100
                ],
                'exception_handler' => [
                    'throw_exceptions' => false
                ]
            ]
        ]
    ]
];
