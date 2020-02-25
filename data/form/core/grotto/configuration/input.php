<?php

return [
    'attributes' => [
        'method' => 'post',
        'action' => 'configuration.php?setting=default&save=save',
        'autocomplete' => false,
        'class' => 'ui form',
        'name' => 'data-setup',
        'id' => 'data-setup'
    ],
    'options' => [
        'label' => 'form.label',
        'translator_text_domain' => 'form-core-grotto-configuration', // This is necesary for translate all labels of form
        // Csrf element (to prevent Cross Site Request Forgery attacks)
        // It is extremely recommended to add this element in all forms
        'use_csrf_security' => true, // true|false - Default value is true
        /**
         * Buttons for form, this key can be a bool value or an array.
         *
         * If this key is not present in the form, no button is displayed
         *
         * Can use default buttons or customize in array options for Zend form factory element
         * Can add more buttons if need
         *
         * Order of render submit, reset and others
         */
        'buttons' => [
            'submit' => true, // true|false - Default value is true
            'reset' => false, // true|false - Default value is false
            // Example of custom button
            // 'example' => [
            //     'name' => 'example',
            //     'type' => 'button',
            //     'attributes' => [
            //         'id' => 'button',
            //         'class' => 'ui button'
            //     ],
            //     'options' => [
            //         'label' => 'button.example',
            //         'translator_text_domain' => 'app-form'
            //     ]
            // ]
        ],
        // This will add the default buttons.
        // 'buttons' => true,
    ],
    'fieldsets' => [
        [
            // Game Setup
            'spec' => include 'data/form/core/grotto/configuration/input/game_setup.php',
        ],
        [
            // Maintenance
            'spec' => include 'data/form/core/grotto/configuration/input/maintenance.php'
        ],
        [
            // Main Page
            'spec' =>  include 'data/form/core/grotto/configuration/input/home.php',
        ],
        [
            // Beta
            'spec' => include 'data/form/core/grotto/configuration/input/beta.php'
        ],
        [
            // Account Creation
            'spec' => include 'data/form/core/grotto/configuration/input/account.php'
        ],
        [
            // Commentary/Chat
            'spec' =>  include 'data/form/core/grotto/configuration/input/commentary.php'
        ],
        [
            // Place names and People names
            'spec' =>  include 'data/form/core/grotto/configuration/input/places.php'
        ],
        [
            // SU titles
            'spec' => include 'data/form/core/grotto/configuration/input/su_title.php'
        ],
        [
            // Referral Settings
            'spec' => include 'data/form/core/grotto/configuration/input/referrañ.php'
        ],
        [
            // Random events
            'spec' => include 'data/form/core/grotto/configuration/input/events.php'
        ],
        [
            // Paypal and Donations
            'spec' => include 'data/form/core/grotto/configuration/input/donation.php'
        ],
        [
            // General Combat
            'spec' => include 'data/form/core/grotto/configuration/input/combat.php'
        ],
        [
            // Training & Levelling
            'spec' => include 'data/form/core/grotto/configuration/input/training.php'
        ],
        [
            // Clans
            'spec' => include 'data/form/core/grotto/configuration/input/clans.php'
        ],
        [
            // New Days
            'spec' => include 'data/form/core/grotto/configuration/input/newdays.php'
        ],
        [
            // Forest
            'spec' => include 'data/form/core/grotto/configuration/input/forest.php'
        ],
        [
            // Multiple Enemies
            'spec' => include 'data/form/core/grotto/configuration/input/enemies.php'
        ],
        [
            // Companions/Mercenaries
            'spec' => include 'data/form/core/grotto/configuration/input/companion.php'
        ],
        [
            // Bank Settings
            'spec' => include 'data/form/core/grotto/configuration/input/bank.php'
        ],
        [
            // Mail Settings
            'spec' => include 'data/form/core/grotto/configuration/input/mail.php'
        ],
        [
            // PvP
            'spec' => include 'data/form/core/grotto/configuration/input/pvp.php'
        ],
        [
            // Content Expiration
            'spec' => include 'data/form/core/grotto/configuration/input/content.php'
        ],
        [
            // LoGDnet Setup
            'spec' => include 'data/form/core/grotto/configuration/input/logdnet.php'
        ],
        [
            // Game day Setup
            'spec' => include 'data/form/core/grotto/configuration/input/daysetup.php'
        ],
        [
            // Miscellaneous Settings
            'spec' => include 'data/form/core/grotto/configuration/input/misc.php'
        ],
    ],
    'input_filter' => 'Lotgd\Core\Form\Filter\Configuration'
];
