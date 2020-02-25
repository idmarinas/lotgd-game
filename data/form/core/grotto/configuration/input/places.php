<?php

return [
    'name' => 'places',
    'attributes' => [
        'id' => 'places'
    ],
    'options' => [
        'label' => 'places.title'
    ],
    'elements' => [
        // Name for the main village
        [
            'spec' => [
                'type' => 'text',
                'name' => 'villagename',
                'attributes' => [
                    'id' => 'villagename',
                    'required' => true
                ],
                'options' => [
                    'label' => 'places.villagename',
                ]
            ]
        ],
        // Name of the inn
        [
            'spec' => [
                'type' => 'text',
                'name' => 'innname',
                'attributes' => [
                    'id' => 'innname',
                    'required' => true
                ],
                'options' => [
                    'label' => 'places.innname',
                ]
            ]
        ],
        // Name of the barkeep
        [
            'spec' => [
                'type' => 'text',
                'name' => 'barkeep',
                'attributes' => [
                    'id' => 'barkeep',
                    'required' => true
                ],
                'options' => [
                    'label' => 'places.barkeep',
                ]
            ]
        ],
        // Name of the barmaid
        [
            'spec' => [
                'type' => 'text',
                'name' => 'barmaid',
                'attributes' => [
                    'id' => 'barmaid',
                    'required' => true
                ],
                'options' => [
                    'label' => 'places.barmaid',
                ]
            ]
        ],
        // Name of the bard
        [
            'spec' => [
                'type' => 'text',
                'name' => 'bard',
                'attributes' => [
                    'id' => 'bard',
                    'required' => true
                ],
                'options' => [
                    'label' => 'places.bard',
                ]
            ]
        ],
        // Name of the clan registrar
        [
            'spec' => [
                'type' => 'text',
                'name' => 'clanregistrar',
                'attributes' => [
                    'id' => 'clanregistrar',
                    'required' => true
                ],
                'options' => [
                    'label' => 'places.clanregistrar',
                ]
            ]
        ],
        // Name of the banker
        [
            'spec' => [
                'type' => 'text',
                'name' => 'bankername',
                'attributes' => [
                    'id' => 'bankername',
                    'required' => true
                ],
                'options' => [
                    'label' => 'places.bankername',
                ]
            ]
        ],
        // Name of the death overlord
        [
            'spec' => [
                'type' => 'text',
                'name' => 'deathoverlord',
                'attributes' => [
                    'id' => 'deathoverlord',
                    'required' => true
                ],
                'options' => [
                    'label' => 'places.deathoverlord',
                ]
            ]
        ],
    ]
];
