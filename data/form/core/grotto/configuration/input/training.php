<?php

return [
    'name' => 'training',
    'attributes' => [
        'id' => 'training'
    ],
    'options' => [
        'label' => 'training.title'
    ],
    'elements' => [
        // Masters hunt down truant students
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'automaster',
                'attributes' => [
                    'id' => 'automaster',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'training.automaster',
                ]
            ]
        ],
        // Can players gain multiple levels (challenge multiple masters) per game day?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'multimaster',
                'attributes' => [
                    'id' => 'multimaster',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'training.multimaster',
                ]
            ]
        ],
        // Display news if somebody fought his master?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'displaymasternews',
                'attributes' => [
                    'id' => 'displaymasternews',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'training.displaymasternews.label',
                    'note' => 'training.displaymasternews.note'
                ]
            ]
        ],
        // Which is the maximum attainable level (at which also the Dragon shows up)?
        [
            'spec' => [
                'type' => 'number',
                'name' => 'maxlevel',
                'attributes' => [
                    'id' => 'maxlevel',
                ],
                'options' => [
                    'label' => 'training.maxlevel.label',
                    'note' => 'training.maxlevel.note'
                ]
            ]
        ],
        // Give here what experience is necessary for each level
        [
            'spec' => [
                'type' => 'text',
                'name' => 'exp-array',
                'attributes' => [
                    'id' => 'exp-array',
                ],
                'options' => [
                    'label' => 'training.exp.array.label',
                    'note' => 'training.exp.array.note'
                ]
            ]
        ],
    ]
];
