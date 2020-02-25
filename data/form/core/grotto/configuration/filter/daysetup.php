<?php

use Zend\Filter;
use Zend\Validator;

return [
    'type' => \Zend\InputFilter\InputFilter::class,
    [
        'name' => 'gametime',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 0,
                    'max' => 100
                ],
            ],
        ]
    ],
    [
        'name' => 'gameoffsetseconds',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Zend\I18n\Validator\IsInt::class]
        ]
    ],
];
