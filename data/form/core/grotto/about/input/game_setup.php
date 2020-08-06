<?php

return [
    'name' => 'game_setup',
    'attributes' => [
        'id' => 'game_setup',
    ],
    'options' => [
        'label' => 'game.setup.title'
    ],
    'elements' => [
        //-- Enable Slay Other Players
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'pvp',
                'attributes' => [
                    'id' => 'pvp'
                ],
                'options' => [
                    'label' => 'game.setup.pvp',
                    'show_inline' => true,
                    'apply_filter' => 'affirmation_negation'
                ]
            ]
        ],
        //-- Player Fights per day
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'pvpday',
                'attributes' => [
                    'id' => 'pvpday'
                ],
                'options' => [
                    'label' => 'game.setup.pvpday',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Days that new players are safe from PvP
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'pvpimmunity',
                'attributes' => [
                    'id' => 'pvpimmunity'
                ],
                'options' => [
                    'label' => 'game.setup.pvpimmunity',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Amount of experience when players become killable in PvP
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'pvpminexp',
                'attributes' => [
                    'id' => 'pvpminexp'
                ],
                'options' => [
                    'label' => 'game.setup.pvpminexp',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Clean user posts (filters bad language and splits words over 45 chars long)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'soap',
                'attributes' => [
                    'id' => 'soap'
                ],
                'options' => [
                    'label' => 'game.setup.soap',
                    'show_inline' => true,
                    'apply_filter' => 'affirmation_negation'
                ]
            ]
        ],
        //-- Amount of gold to start a new character with
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'newplayerstartgold',
                'attributes' => [
                    'id' => 'newplayerstartgold'
                ],
                'options' => [
                    'label' => 'game.setup.newplayerstartgold',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
    ],
];
