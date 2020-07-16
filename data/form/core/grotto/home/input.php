
<?php

return [
    'attributes' => [
        'method' => 'post',
        'action' => 'home.php',
        'autocomplete' => false,
        'class' => 'ui center aligned form',
        'name' => 'home',
        'id' => 'home'
    ],
    'options' => [
        'translator_text_domain' => 'form-core-grotto-home', // This is necesary for translate all labels of form
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
        ],
    ],
    'elements' => [
        [
            'spec' => [
                'type' => 'lotgdTheme',
                'name' => 'defaultskin',
                'attributes' => [
                    'id' => 'defaultskin',
                ],
                'options' => [
                    'label' => 'home.defaultskin'
                ]
            ]
        ]
    ]
];
