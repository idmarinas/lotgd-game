<?php

return [
    'name' => 'daysetup',
    'attributes' => [
        'id' => 'daysetup'
    ],
    'options' => [
        'label' => 'daysetup.title'
    ],
    'elements' => [
        // Show the village game time in what format?
        [
            'spec' => [
                'type' => 'text',
                'name' => 'gametime',
                'attributes' => [
                    'id' => 'gametime',
                ],
                'options' => [
                    'label' => 'daysetup.gametime.label',
                    'note' => 'daysetup.gametime.note'
                ]
            ]
        ],
        // Day Duration
        [
            'spec' => [
                'type' => 'text',
                'name' => 'dayduration',
                'attributes' => [
                    'id' => 'dayduration',
                    'disabled' => true,
                ],
                'options' => [
                    'label' => 'daysetup.dayduration'
                ]
            ]
        ],
        // Current game time
        [
            'spec' => [
                'type' => 'text',
                'name' => 'curgametime',
                'attributes' => [
                    'id' => 'curgametime',
                    'disabled' => true,
                ],
                'options' => [
                    'label' => 'daysetup.curgametime'
                ]
            ]
        ],
        // Current Server Time
        [
            'spec' => [
                'type' => 'text',
                'name' => 'curservertime',
                'attributes' => [
                    'id' => 'curservertime',
                    'disabled' => true,
                ],
                'options' => [
                    'label' => 'daysetup.curservertime'
                ]
            ]
        ],
        // Last new day
        [
            'spec' => [
                'type' => 'text',
                'name' => 'lastnewday',
                'attributes' => [
                    'id' => 'lastnewday',
                    'disabled' => true,
                ],
                'options' => [
                    'label' => 'daysetup.lastnewday'
                ]
            ]
        ],
        // Next new day
        [
            'spec' => [
                'type' => 'text',
                'name' => 'nextnewday',
                'attributes' => [
                    'id' => 'nextnewday',
                    'disabled' => true,
                ],
                'options' => [
                    'label' => 'daysetup.nextnewday'
                ]
            ]
        ],
        // Real time to offset new day
        [
            'spec' => [
                'type' => 'number',
                'name' => 'gameoffsetseconds',
                'attributes' => [
                    'id' => 'gameoffsetseconds',
                ],
                'options' => [
                    'label' => 'daysetup.gameoffsetseconds'
                ]
            ]
        ],
    ]
];
