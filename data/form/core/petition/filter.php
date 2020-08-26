
<?php

use Laminas\Validator;
use Laminas\Filter;

return [
    [
        'name' => 'charname',
        'required' => true,
        'filters' => [
            ['name' => Filter\StripTags::class],
            ['name' => Filter\StripNewlines::class],
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 0,
                    'max' => 100
                ]
            ],
        ]
    ],
    [
        'name' => 'email',
        'required' => true,
        'filters' => [],
        'validators' => [
            ['name' => Validator\EmailAddress::class],
        ]
    ],
    [
        'name' => 'problem_type',
        'required' => true,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
        ]
    ],
    [
        'name' => 'description',
        'required' => true,
        'filters' => [
            ['name' => Filter\StripTags::class]
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
        ]
    ],
];
