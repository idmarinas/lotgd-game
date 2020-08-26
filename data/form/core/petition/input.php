
<?php

return [
    'attributes' => [
        'method'       => '',
        'action'       => '',
        'autocomplete' => false,
        'class'        => 'ui form',
        'name'         => 'petition',
        'id'           => 'petition',
    ],
    'options' => [
        'translator_text_domain' => 'form-core-petition', // This is necesary for translate all labels of form
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
        'buttons' => false,
    ],
    'elements' => [
        [
            'spec' => [
                'type'       => 'text',
                'name'       => 'charname',
                'attributes' => [
                    'id' => 'charname',
                ],
                'options' => [
                    'label' => 'charname',
                ],
            ],
        ],
        [
            'spec' => [
                'type'       => 'email',
                'name'       => 'email',
                'attributes' => [
                    'id' => 'email',
                ],
                'options' => [
                    'label' => 'email',
                ],
            ],
        ],
        [
            'spec' => [
                'type'       => 'petitiontype',
                'name'       => 'problem_type',
                'attributes' => [
                    'id' => 'problem_type',
                ],
                'options' => [
                    'label'                  => 'petition.type',
                    'translator_text_domain' => 'jaxon-petition',
                ],
            ],
        ],
        [
            'spec' => [
                'type'       => 'textarea',
                'name'       => 'description',
                'attributes' => [
                    'id' => 'description',
                ],
                'options' => [
                    'label' => 'description',
                ],
            ],
        ],
    ],
    'input_filter' => 'Lotgd\Core\Form\Filter\Petition',
];
