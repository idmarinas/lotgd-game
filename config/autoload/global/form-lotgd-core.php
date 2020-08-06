<?php

/**
 * Create forms via Service Manager and Form Factory
 */

return [
    'forms' => [
        //-- Added in version 4.1.0
        'Lotgd\Core\Form\Configuration' => include 'data/form/core/grotto/configuration/input.php',
        //-- Added in version 4.3.0
        'Lotgd\Core\Form\Cronjob' => include 'data/form/core/grotto/cronjob/input.php',
        'Lotgd\Core\Form\HomeSkin' => include 'data/form/core/grotto/home/input.php',
        'Lotgd\Core\Form\About' => include 'data/form/core/grotto/about/input.php',
    ]
];
