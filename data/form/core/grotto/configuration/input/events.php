<?php

return [
    'name' => 'events',
    'attributes' => [
        'id' => 'events'
    ],
    'options' => [
        'label' => 'events.title'
    ],
    'elements' => [
        // Chance for Something Special in the Forest
        [
            'spec' => [
                'type' => 'range',
                'name' => 'forestchance',
                'attributes' => [
                    'id' => 'forestchance',
                    'min' => 0,
                    'max' => 100,
                    'value' => 10
                ],
                'options' => [
                    'label' => 'events.forestchance',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Chance for Something Special in any village
        [
            'spec' => [
                'type' => 'range',
                'name' => 'villagechance',
                'attributes' => [
                    'id' => 'villagechance',
                    'min' => 0,
                    'max' => 100,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'events.villagechance',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Chance for Something Special in the Inn
        [
            'spec' => [
                'type' => 'range',
                'name' => 'innchance',
                'attributes' => [
                    'id' => 'innchance',
                    'min' => 0,
                    'max' => 100,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'events.innchance',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Chance for Something Special in the Graveyard
        [
            'spec' => [
                'type' => 'range',
                'name' => 'gravechance',
                'attributes' => [
                    'id' => 'gravechance',
                    'min' => 0,
                    'max' => 100,
                    'value' => 10
                ],
                'options' => [
                    'label' => 'events.gravechance',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Chance for Something Special in the Gardens
        [
            'spec' => [
                'type' => 'range',
                'name' => 'gardenchance',
                'attributes' => [
                    'id' => 'gardenchance',
                    'min' => 0,
                    'max' => 100,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'events.gardenchance',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
    ]
];
