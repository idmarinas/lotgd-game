<?php

return [
    'name' => 'misc',
    'attributes' => [
        'id' => 'misc'
    ],
    'options' => [
        'label' => 'misc.title'
    ],
    'elements' => [
        // Cost to resurrect from the dead?
        [
            'spec' => [
                'type' => 'number',
                'name' => 'resurrectioncost',
                'attributes' => [
                    'id' => 'resurrectioncost',
                    'required' => false,
                    'value' => 100
                ],
                'options' => [
                    'label' => 'misc.resurrectioncost'
                ]
            ]
        ],
        // The Barkeeper may help you to switch your specialty?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'allowspecialswitch',
                'attributes' => [
                    'id' => 'allowspecialswitch',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'misc.allowspecialswitch',
                ]
            ]
        ],
        // Maximum number of items to be shown in the warrior list
        [
            'spec' => [
                'type' => 'number',
                'name' => 'maxlistsize',
                'attributes' => [
                    'id' => 'maxlistsize',
                    'required' => false,
                    'value' => 25
                ],
                'options' => [
                    'label' => 'misc.maxlistsize'
                ]
            ]
        ],
        // Does Merick have feed onhand for creatures
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'allowfeed',
                'attributes' => [
                    'id' => 'allowfeed',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'misc.allowfeed',
                ]
            ]
        ],
    ]
];
