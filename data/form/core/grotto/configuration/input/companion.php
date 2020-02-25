<?php

return [
    'name' => 'companion',
    'attributes' => [
        'id' => 'companion'
    ],
    'options' => [
        'label' => 'companion.title'
    ],
    'elements' => [
        // Enable the usage of companions
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'enablecompanions',
                'attributes' => [
                    'id' => 'enablecompanions',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'companion.enablecompanions',
                ]
            ]
        ],
        // How many companions are allowed per player
        [
            'spec' => [
                'type' => 'number',
                'name' => 'companionsallowed',
                'attributes' => [
                    'id' => 'companionsallowed',
                    'value' => 1
                ],
                'options' => [
                    'label' => 'companion.companionsallowed.label',
                    'note' => 'companion.companionsallowed.note',
                ]
            ]
        ],
        // Are companions allowed to level up?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'companionslevelup',
                'attributes' => [
                    'id' => 'companionslevelup',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'companion.companionslevelup',
                ]
            ]
        ],
    ]
];
