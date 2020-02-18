<?php

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Service;

return [
    'hydrators' => [
        'factories' => [
            DoctrineObject::class => Service\DoctrineObjectHydratorFactory::class
        ],
    ],
];
