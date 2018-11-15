<?php

return [
    'config_glob_paths' => [
        __DIR__ . '/autoload/global/{**/*,*}.php',
        __DIR__ . '/autoload/local/{**/*,*}.php'
    ],
    'lotgd_core' => [
        'cache_config' => true //-- This cache config of service manager recomended in production
    ]
];
