<?php

use Laminas\Filter;
use Laminas\Validator;

return [
    'type' => \Laminas\InputFilter\InputFilter::class,
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
            ['name' => Laminas\I18n\Validator\IsInt::class]
        ]
    ],
];
