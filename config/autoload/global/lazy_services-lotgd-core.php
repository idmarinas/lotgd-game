<?php

return [
    'service_manager' => [
        'lazy_services' => [
            'class_map' => [
                //-- This method of connecting to the database is rarely used.
                'LaminasDb' => Lotgd\Core\Db\Dbwrapper::class,
                //-- Laminas Form not likely to be used in all requests
                'InputFilterManager'    => 'InputFilterManager',
                'FormAnnotationBuilder' => 'FormAnnotationBuilder',
                'FormElementManager'    => 'FormElementManager',
            ],

            // directory where proxy classes will be written - default to system_get_tmp_dir()
            'proxies_target_dir' => 'storage/cache/proxy',

            // whether the generated proxy classes should be written to disk or generated on-the-fly
            'write_proxy_files' => true,
        ],
    ],
];
