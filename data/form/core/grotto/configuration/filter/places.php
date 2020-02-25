<?php

use Zend\Filter;
use Zend\Validator;

return [
    'type' => \Zend\InputFilter\InputFilter::class,
    [
        'name' => 'villagename',
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
        'name' => 'innname',
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
        'name' => 'barkeep',
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
        'name' => 'barmaid',
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
        'name' => 'bard',
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
        'name' => 'clanregistrar',
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
        'name' => 'deathoverlord',
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
        'name' => 'bankername',
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
];
