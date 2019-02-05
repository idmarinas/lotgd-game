<?php

return [
    'service_manager' => [
        'aliases' => [
            Zend\Session\SessionManager::class => Zend\Session\ManagerInterface::class,
        ]
    ]
];
