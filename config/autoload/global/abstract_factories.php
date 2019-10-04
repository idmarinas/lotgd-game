<?php

return [
    'service_manager' => [
        'abstract_factories' => [
            \Zend\Session\Service\ContainerAbstractServiceFactory::class,
            \Zend\Cache\Service\StorageCacheAbstractServiceFactory::class
        ]
    ]
];
