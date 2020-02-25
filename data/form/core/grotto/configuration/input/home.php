<?php

return [
    'name' => 'home',
    'attributes' => [
        'id' => 'home'
    ],
    'options' => [
        'label' => 'home.title'
    ],
    'elements' => [
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'homeskinselect',
                'attributes' => [
                    'id' => 'homeskinselect',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'home.homeskinselect'
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'homecurtime',
                'attributes' => [
                    'id' => 'homecurtime',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'home.homecurtime'
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'homenewdaytime',
                'attributes' => [
                    'id' => 'homenewdaytime',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'home.homenewdaytime'
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'homenewestplayer',
                'attributes' => [
                    'id' => 'homenewestplayer',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'home.homenewestplayer'
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'lotgdTheme',
                'name' => 'defaultskin',
                'attributes' => [
                    'id' => 'defaultskin',
                ],
                'options' => [
                    'label' => 'home.defaultskin'
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'textarea',
                'name' => 'impressum',
                'attributes' => [
                    'id' => 'impressum',
                ],
                'options' => [
                    'label' => 'home.impressum'
                ]
            ]
        ],
    ]
];
