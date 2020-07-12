<?php

use Laminas\Filter;
use Laminas\Validator;

return [
    'type' => \Laminas\InputFilter\InputFilter::class,
    [
        'name' => 'enable_chat_tags',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
        ]
    ],
    [
        'name' => 'chat_tag_megauser',
        'required' => false,
        'filters' => [
            ['name' => Filter\StringTrim::class],
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 3,
                    'max' => 255
                ],
            ],
        ],
    ],
    [
        'name' => 'chat_tag_gm',
        'required' => false,
        'filters' => [
            ['name' => Filter\StringTrim::class],
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 3,
                    'max' => 255
                ],
            ],
        ],
    ],
    [
        'name' => 'chat_tag_mod',
        'required' => false,
        'filters' => [
            ['name' => Filter\StringTrim::class],
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 3,
                    'max' => 255
                ],
            ],
        ],
    ],
];
