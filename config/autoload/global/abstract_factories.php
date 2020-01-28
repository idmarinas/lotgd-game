<?php

return [
    'service_manager' => [
        'abstract_factories' => [
            \Zend\Session\Service\ContainerAbstractServiceFactory::class,
            \Zend\Cache\Service\StorageCacheAbstractServiceFactory::class,

            //-- Added in version 4.1.0
            \Zend\Form\FormAbstractServiceFactory::class,
        ]
    ],
    'input_filters' => [
        'abstract_factories' => [
            //-- Added in version 4.1.0
            \Zend\InputFilter\InputFilterAbstractServiceFactory::class,
        ],
    ],
];
