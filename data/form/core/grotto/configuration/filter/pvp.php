<?php

use Laminas\Filter;
use Laminas\Validator;

return [
    'type' => \Laminas\InputFilter\InputFilter::class,
    [
        'name' => 'pvp',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvptimeout',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvpday',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvpdragonoptout',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvprange',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvpimmunity',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvpminexp',
        'required' => false,
        'filters' => [
            ['name' => Filter\ToFloat::class ]
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Laminas\I18n\Validator\IsFloat::class,
            ]
        ]
    ],
    [
        'name' => 'pvpattgain',
        'required' => false,
        'filters' => [
            ['name' => Filter\ToFloat::class ]
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Laminas\I18n\Validator\IsFloat::class,
            ]
        ]
    ],
    [
        'name' => 'pvpattlose',
        'required' => false,
        'filters' => [
            ['name' => Filter\ToFloat::class ]
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Laminas\I18n\Validator\IsFloat::class,
            ]
        ]
    ],
    [
        'name' => 'pvpdefgain',
        'required' => false,
        'filters' => [
            ['name' => Filter\ToFloat::class ]
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Laminas\I18n\Validator\IsFloat::class,
            ]
        ]
    ],
    [
        'name' => 'pvpdeflose',
        'required' => false,
        'filters' => [
            ['name' => Filter\ToFloat::class ]
        ],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Laminas\I18n\Validator\IsFloat::class,
            ]
        ]
    ],
    [
        'name' => 'pvphardlimit',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvphardlimitamount',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvpsameid',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'pvpsameip',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
];
