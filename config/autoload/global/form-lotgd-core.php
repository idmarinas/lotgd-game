<?php

/**
 * Create forms via Service Manager and Form Factory.
 */

return [
    'forms' => [
        //-- Added in version 4.3.0
        'Lotgd\Core\Form\Cronjob'  => include 'data/form/core/grotto/cronjob/input.php',
        'Lotgd\Core\Form\HomeSkin' => include 'data/form/core/grotto/home/input.php',
        'Lotgd\Core\Form\About'    => include 'data/form/core/grotto/about/input.php',
        'Lotgd\Core\Form\Petition' => include 'data/form/core/petition/input.php',
    ],
];
