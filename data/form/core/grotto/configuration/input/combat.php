<?php

return [
    'name' => 'combat',
    'attributes' => [
        'id' => 'combat'
    ],
    'options' => [
        'label' => 'combat.title'
    ],
    'elements' => [
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'autofight',
                'attributes' => [
                    'id' => 'autofight',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'combat.autofight',
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'select',
                'name' => 'autofightfull',
                'attributes' => [
                    'id' => 'autofightfull',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'combat.autofightfull.label',
                    'value_options' => [
                        0 => 'combat.autofightfull.option.never',
                        1 => 'combat.autofightfull.option.always',
                        2 => 'combat.autofightfull.option.flee',
                    ]
                ]
            ]
        ],
    ]
    ];
