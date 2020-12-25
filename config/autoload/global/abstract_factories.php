<?php

return [
    'service_manager' => [
        'abstract_factories' => [
            \Laminas\Cache\Service\StorageCacheAbstractServiceFactory::class,

            //-- Added in version 4.1.0
            \Laminas\Form\FormAbstractServiceFactory::class,
        ],
    ],
    'input_filters' => [
        'abstract_factories' => [
            //-- Added in version 4.1.0
            \Laminas\InputFilter\InputFilterAbstractServiceFactory::class,
        ],
    ],
];
