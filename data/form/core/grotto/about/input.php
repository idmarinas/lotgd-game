<?php

return [
    'attributes' => [
        'method' => 'none',
        'class' => 'ui form',
        'name' => 'about',
        'id' => 'about'
    ],
    'options' => [
        'label' => 'form.label',
        'translator_text_domain' => 'form-core-grotto-about', // This is necesary for translate all labels of form
        'use_csrf_security' => false,
        'buttons' => false,
    ],
    'fieldsets' => [
        [
            // Game Setup
            'spec' => include 'data/form/core/grotto/about/input/game_setup.php',
        ],
        [
            // New Days
            'spec' => include 'data/form/core/grotto/about/input/newday.php',
        ],
        [
            // Bank settings
            'spec' => include 'data/form/core/grotto/about/input/bank.php',
        ],
        [
            // Forest
            'spec' => include 'data/form/core/grotto/about/input/forest.php',
        ],
        [
            // Mail Settings
            'spec' => include 'data/form/core/grotto/about/input/mail.php',
        ],
        [
            // Content Expiration
            'spec' => include 'data/form/core/grotto/about/input/content.php',
        ],
        [
            // Useful Information
            'spec' => include 'data/form/core/grotto/about/input/info.php',
        ],
    ]
];
