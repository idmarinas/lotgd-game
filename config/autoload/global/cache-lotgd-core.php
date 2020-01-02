<?php

/**
 * You can create your own cache system (or several) following the example of the LoTGD Core cache
 */

return [
    'caches' => [
        //-- Please not detele this Data Cache
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
				[
					'name' => 'serializer',
					'options' => []
				],
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
				[
					'name' => 'serializer',
					'options' => []
				],
                'exception_handler' => [
                    'throw_exceptions' => false
                ],
            ]
        ],
    ]
];
