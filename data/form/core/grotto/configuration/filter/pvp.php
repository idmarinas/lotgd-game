<?php

use Zend\Filter;
use Zend\Validator;

return [
    'type' => \Zend\InputFilter\InputFilter::class,
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
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Zend\I18n\Validator\IsFloat::class,
                'options' => [
                    'locale' => 'en'
                ]
            ]
        ]
    ],
    [
        'name' => 'pvpattgain',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Zend\I18n\Validator\IsFloat::class,
                'options' => [
                    'locale' => 'en'
                ]
            ]
        ]
    ],
    [
        'name' => 'pvpattlose',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Zend\I18n\Validator\IsFloat::class,
                'options' => [
                    'locale' => 'en'
                ]
            ]
        ]
    ],
    [
        'name' => 'pvpdefgain',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Zend\I18n\Validator\IsFloat::class,
                'options' => [
                    'locale' => 'en'
                ]
            ]
        ]
    ],
    [
        'name' => 'pvpdeflose',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            [
                'name' => Zend\I18n\Validator\IsFloat::class,
                'options' => [
                    'locale' => 'en'
                ]
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
