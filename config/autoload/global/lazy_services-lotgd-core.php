<?php

return [
    'service_manager' => [
        'lazy_services' => [
            'class_map' => [
                //-- Added in version 4.4.0

                //-- This method of connecting to the database is rarely used.
                'LaminasDb' => Lotgd\Core\Db\Dbwrapper::class,
                //-- Symfony Form is only needed for entity modification mainly.
                'Lotgd\Core\SymfonyForm' => 'Lotgd\Core\SymfonyForm',
                //-- Laminas Form not likely to be used in all requests
                'InputFilterManager'    => 'InputFilterManager',
                'FormAnnotationBuilder' => 'FormAnnotationBuilder',
                'FormElementManager'    => 'FormElementManager',

                //-- Added in version 4.8.0
                'EventOfEvents' => Lotgd\Core\EventManager\Event::class,
            ],

            // directory where proxy classes will be written - default to system_get_tmp_dir()
            'proxies_target_dir' => 'storage/cache/proxy',

            // whether the generated proxy classes should be written to disk or generated on-the-fly
            'write_proxy_files' => true,
        ],
    ],
];
