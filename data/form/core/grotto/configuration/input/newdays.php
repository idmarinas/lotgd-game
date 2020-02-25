<?php

return [
    'name' => 'newdays',
    'attributes' => [
        'id' => 'newdays'
    ],
    'options' => [
        'label' => 'newdays.title'
    ],
    'elements' => [
        // Game days per calendar day
        [
            'spec' => [
                'type' => 'range',
                'name' => 'daysperday',
                'attributes' => [
                    'id' => 'daysperday',
                    'min' => 1,
                    'max' => 24,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'newdays.daysperday'
                ]
            ]
        ],
        // Extra daily uses in specialty area
        [
            'spec' => [
                'type' => 'range',
                'name' => 'specialtybonus',
                'attributes' => [
                    'id' => 'specialtybonus',
                    'min' => 0,
                    'max' => 5,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'newdays.specialtybonus'
                ]
            ]
        ],
        // Modify (+ or -) the number of turns deducted after a resurrection as an absolute (number) or relative (number followed by %)
        [
            'spec' => [
                'type' => 'text',
                'name' => 'resurrectionturns',
                'attributes' => [
                    'id' => 'resurrectionturns'
                ],
                'options' => [
                    'label' => 'newdays.resurrectionturns'
                ]
            ]
        ],
        // What weapon is standard for new players or players who just killed the dragon?
        [
            'spec' => [
                'type' => 'text',
                'name' => 'startweapon',
                'attributes' => [
                    'id' => 'startweapon'
                ],
                'options' => [
                    'label' => 'newdays.startweapon'
                ]
            ]
        ],
        // What armor is standard for new players or players who just killed the dragon?
        [
            'spec' => [
                'type' => 'text',
                'name' => 'startarmor',
                'attributes' => [
                    'id' => 'startarmor'
                ],
                'options' => [
                    'label' => 'newdays.startarmor'
                ]
            ]
        ],
    ]
];
