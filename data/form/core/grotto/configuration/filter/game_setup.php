<?php

use Laminas\Filter;
use Laminas\Validator;

return [
    'type' => \Laminas\InputFilter\InputFilter::class, //-- Need this when is a Field set
    [
        'name' => 'allowcreation',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'servername',
        'required' => true,
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
        'name' => 'serverurl',
        'required' => false,
        'filters' => [
            ['name' => Filter\StringTrim::class],
            [
                'name' => Filter\UriNormalize::class,
                'options' => [
                    'enforcedScheme' => 'https'
                ]
            ]
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Uri::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 0,
                    'max' => 255
                ],
            ],
        ]
    ],
    [
        'name' => 'loginbanner',
        'required' => false,
        'filters' => [
            [
                'name' => Filter\StringTrim::class
            ],
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 0,
                    'max' => 255
                ],
            ],
        ]
    ],
    [
        'name' => 'maxonline',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'gameadminemail',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\EmailAddress::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 0,
                    'max' => 255
                ],
            ],
        ]
    ],
    [
        'name' => 'emailpetitions',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'petition_types',
        'required' => true,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Validator\StringLength::class,
                'options' => [
                    'min' => 0,
                    'max' => 255
                ],
            ],
        ]
    ],
    [
        'name' => 'edittitles',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'motditems',
        'required' => true,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ]
];
