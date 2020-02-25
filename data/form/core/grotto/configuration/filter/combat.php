<?php

use Zend\Filter;
use Zend\Validator;

return [
    'type' => \Zend\InputFilter\InputFilter::class,
    [
        'name' => 'autofight',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ],
    [
        'name' => 'autofightfull',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class]
        ]
    ],
];
