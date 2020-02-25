<?php

return [
    'name' => 'clans',
    'attributes' => [
        'id' => 'clans'
    ],
    'options' => [
        'label' => 'clans.title'
    ],
    'elements' => [
        // Enable Clan System?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'allowclans',
                'attributes' => [
                    'id' => 'allowclans',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'clans.allowclans',
                ]
            ]
        ],
        // Gold to start a clan
        [
            'spec' => [
                'type' => 'number',
                'name' => 'goldtostartclan',
                'attributes' => [
                    'id' => 'goldtostartclan',
                    'value' => 10000
                ],
                'options' => [
                    'label' => 'clans.goldtostartclan',
                ]
            ]
        ],
        // Gems to start a clan
        [
            'spec' => [
                'type' => 'number',
                'name' => 'gemstostartclan',
                'attributes' => [
                    'id' => 'gemstostartclan',
                    'value' => 15
                ],
                'options' => [
                    'label' => 'clans.gemstostartclan',
                ]
            ]
        ],
        // Can clan officers who are also moderators moderate their own clan even if they cannot moderate all clans?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'officermoderate',
                'attributes' => [
                    'id' => 'officermoderate',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'clans.officermoderate',
                ]
            ]
        ],
        // Hard sanitize for all but latin chars  in the clan name at creation?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'clannamesanitize',
                'attributes' => [
                    'id' => 'clannamesanitize',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'clans.clannamesanitize',
                ]
            ]
        ],
        // Hard sanitizie for all but latin chars in the short name at creation?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'clanshortnamesanitize',
                'attributes' => [
                    'id' => 'clanshortnamesanitize',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'clans.clanshortnamesanitize',
                ]
            ]
        ],
        // Length of the short name (max 20)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'clanshortnamelength',
                'attributes' => [
                    'id' => 'clanshortnamelength',
                    'value' => 3
                ],
                'options' => [
                    'label' => 'clans.clanshortnamelength',
                ]
            ]
        ],
    ]
];
