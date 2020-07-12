<?php

use Laminas\Filter;
use Laminas\Validator;

return [
    'type' => \Laminas\InputFilter\InputFilter::class,
    [
        'name' => 'mailsizelimit',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'inboxlimit',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'oldmail',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'superuseryommessage',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 3,
                    'max' => 255
                ],
            ],
        ]
    ],
    [
        'name' => 'onlyunreadmails',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
];
