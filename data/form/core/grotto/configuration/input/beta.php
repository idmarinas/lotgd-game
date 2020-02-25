<?php

return  [
    'name' => 'beta',
    'attributes' => [
        'id' => 'beta'
    ],
    'options' => [
        'label' => 'beta.title'
    ],
    'elements' => [
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'beta',
                'attributes' => [
                    'id' => 'beta',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'beta.beta'
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'betaperplayer',
                'attributes' => [
                    'id' => 'betaperplayer',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'beta.betaperplayer.label',
                    'note' => 'beta.betaperplayer.note',
                ]
            ]
        ],
    ]
];
