<?php

return [
    'service_manager' => [
        'abstract_factories' => [
            \Laminas\Session\Service\ContainerAbstractServiceFactory::class,
            \Laminas\Cache\Service\StorageCacheAbstractServiceFactory::class,

            //-- Added in version 4.1.0
            \Laminas\Form\FormAbstractServiceFactory::class,

            //-- Added in version 4.2.0
            'DoctrineModule' => 'DoctrineModule\ServiceFactory\AbstractDoctrineServiceFactory',
        ]
    ],
    'input_filters' => [
        'abstract_factories' => [
            //-- Added in version 4.1.0
            \Laminas\InputFilter\InputFilterAbstractServiceFactory::class,
        ],
    ],
];
