<?php

return [
    //-- Configuración para las sesiones
    'session_manager' => [
        'enable_default_container_manager' => true,
        'validators' => [
            Zend\Session\Validator\RemoteAddr::class,
            Zend\Session\Validator\HttpUserAgent::class,
		]
    ],
    'session_config' => [
        'name' => 'LegendOfTheGreenDragon',
        'gc_maxlifetime' => 4320,
        'cookie_lifetime' => 4320,
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'remember_me_seconds' => 172800,
        'gc_probability' => 10,
    ],
    'session_storage' => [
        'type' => 'SessionArrayStorage'
    ]
];
