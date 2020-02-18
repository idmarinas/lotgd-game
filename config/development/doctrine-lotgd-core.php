<?php

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'metadata_cache' => 'array',

                'query_cache' => 'array',

                'result_cache' => 'array',

                'hydration_cache' => 'array',

                'generate_proxies' => true,
            ],
        ],

        'driver' => [
            'lotgd_core' => [
                'cache' => 'array',
            ],
            'lotgd_local' => [
                'cache' => 'array',
            ],
        ],
    ],
];
