<?php

return [
    'name' => 'pvp',
    'attributes' => [
        'id' => 'pvp'
    ],
    'options' => [
        'label' => 'pvp.title'
    ],
    'elements' => [
        // Enable Slay Other Players
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'pvp',
                'attributes' => [
                    'id' => 'pvp',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'pvp.pvp',
                ]
            ]
        ],
        // Timeout in seconds to wait after a player was PvP'd
        [
            'spec' => [
                'type' => 'number',
                'name' => 'pvptimeout',
                'attributes' => [
                    'id' => 'pvptimeout',
                    'value' => 900
                ],
                'options' => [
                    'label' => 'pvp.pvptimeout',
                ]
            ]
        ],
        // Player Fights per day
        [
            'spec' => [
                'type' => 'range',
                'name' => 'pvpday',
                'attributes' => [
                    'id' => 'pvpday',
                    'min' => 1,
                    'max' => 10
                ],
                'options' => [
                    'label' => 'pvp.pvpday',
                ]
            ]
        ],
        // Can players be engaged in pvp after a DK until they visit the village again?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'pvpdragonoptout',
                'attributes' => [
                    'id' => 'pvpdragonoptout',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'pvp.pvpdragonoptout',
                ]
            ]
        ],
        // How many levels can attacker & defender be different? (-1=any - lower limit is always +1)
        [
            'spec' => [
                'type' => 'range',
                'name' => 'pvprange',
                'attributes' => [
                    'id' => 'pvprange',
                    'min' => -1,
                    'max' => 15,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'pvp.pvprange.label',
                    'note' => 'pvp.pvprange.note'
                ]
            ]
        ],
        // Days that new players are safe from PvP
        [
            'spec' => [
                'type' => 'range',
                'name' => 'pvpimmunity',
                'attributes' => [
                    'id' => 'pvpimmunity',
                    'min' => 1,
                    'max' => 5,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'pvp.pvpimmunity',
                ]
            ]
        ],
        // Experience below which player is safe from PvP
        [
            'spec' => [
                'type' => 'number',
                'name' => 'pvpminexp',
                'attributes' => [
                    'id' => 'pvpminexp',
                    'value' => 1500
                ],
                'options' => [
                    'label' => 'pvp.pvpminexp',
                ]
            ]
        ],
        // Percent of victim experience attacker gains on win
        [
            'spec' => [
                'type' => 'range',
                'name' => 'pvpattgain',
                'attributes' => [
                    'id' => 'pvpattgain',
                    'min' => 0.25,
                    'max' => 20,
                    'step' => 0.25,
                    'value' => 0.25
                ],
                'options' => [
                    'label' => 'pvp.pvpattgain',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Percent of experience attacker loses on loss
        [
            'spec' => [
                'type' => 'range',
                'name' => 'pvpattlose',
                'attributes' => [
                    'id' => 'pvpattlose',
                    'min' => 0.25,
                    'max' => 20,
                    'step' => 0.25,
                    'value' => 0.25
                ],
                'options' => [
                    'label' => 'pvp.pvpattlose',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Percent of attacker experience defender gains on win
        [
            'spec' => [
                'type' => 'range',
                'name' => 'pvpdefgain',
                'attributes' => [
                    'id' => 'pvpdefgain',
                    'min' => 0.25,
                    'max' => 20,
                    'step' => 0.25,
                    'value' => 0.25
                ],
                'options' => [
                    'label' => 'pvp.pvpdefgain',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Percent of experience defender loses on loss
        [
            'spec' => [
                'type' => 'range',
                'name' => 'pvpdeflose',
                'attributes' => [
                    'id' => 'pvpdeflose',
                    'min' => 0.25,
                    'max' => 20,
                    'step' => 0.25,
                    'value' => 0.25,
                ],
                'options' => [
                    'label' => 'pvp.pvpdeflose',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Is the maximum amount a successful attacker or defender can gain limited?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'pvphardlimit',
                'attributes' => [
                    'id' => 'pvphardlimit',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'pvp.pvphardlimit',
                ]
            ]
        ],
        // If yes - What is the maximum amount of EXP he can get?
        [
            'spec' => [
                'type' => 'number',
                'name' => 'pvphardlimitamount',
                'attributes' => [
                    'id' => 'pvphardlimitamount',
                ],
                'options' => [
                    'label' => 'pvp.pvphardlimitamount',
                ]
            ]
        ],
        // Can players attack others with same ID?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'pvpsameid',
                'attributes' => [
                    'id' => 'pvpsameid',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'pvp.pvpsameid',
                ]
            ]
        ],
        // Can players attack others with same IP?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'pvpsameip',
                'attributes' => [
                    'id' => 'pvpsameip',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'pvp.pvpsameip',
                ]
            ]
        ],
    ]
];
