<?php

use Laminas\Filter;
use Laminas\Validator;

return [
    'type' => \Laminas\InputFilter\InputFilter::class,
    [
        'name' => 'soap',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
        ]
    ],
    [
        'name' => 'maxcolors',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class],
        ]
    ],
    [
        'name' => 'postinglimit',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
        ]
    ],
    [
        'name' => 'chatlinelength',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class],
        ]
    ],
    [
        'name' => 'maxchars',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class],
        ]
    ],
    [
        'name' => 'moderateexcludes',
        'required' => false,
        'filters' => [
            ['name' => Filter\StringTrim::class],
            ['name' => Filter\StripTags::class]
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 0,
                    'max' => 255
                ]
            ],
        ]
    ],
];
