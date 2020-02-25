<?php

return [
    'name' => 'forest',
    'attributes' => [
        'id' => 'forest'
    ],
    'options' => [
        'label' => 'forest.title'
    ],
    'elements' => [
        // Forest Fights per day
        [
            'spec' => [
                'type' => 'range',
                'name' => 'turns',
                'attributes' => [
                    'id' => 'turns',
                    'min' => 5,
                    'max' => 30,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'forest.turns'
                ]
            ]
        ],
        // Forest Creatures show health
        [
            'spec' => [
                'type' => 'select',
                'name' => 'forestcreaturebar',
                'attributes' => [
                    'id' => 'forestcreaturebar'
                ],
                'options' => [
                    'label' => 'forest.forestcreaturebar.label',
                    'note' => 'forest.forestcreaturebar.note',
                    'value_options' => [
                        0 => 'forest.forestcreaturebar.option.text',
                        1 => 'forest.forestcreaturebar.option.bar',
                        2 => 'forest.forestcreaturebar.option.textbar',
                    ]
                ]
            ]
        ],
        // Forest Creatures drop at least 1/4 of max gold
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'dropmingold',
                'attributes' => [
                    'id' => 'dropmingold',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'forest.dropmingold',
                ]
            ]
        ],
        // Allow players to Seek Suicidally?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'suicide',
                'attributes' => [
                    'id' => 'suicide',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'forest.suicide',
                ]
            ]
        ],
        // Minimum DKs before players can Seek Suicidally?
        [
            'spec' => [
                'type' => 'number',
                'name' => 'suicidedk',
                'attributes' => [
                    'id' => 'suicidedk',
                    'value' => 1
                ],
                'options' => [
                    'label' => 'forest.suicidedk.label',
                    'note' => 'forest.suicidedk.note',
                ]
            ]
        ],
        // In one out of how many fight rounds do enemies do a power attack?
        [
            'spec' => [
                'type' => 'range',
                'name' => 'forestpowerattackchance',
                'attributes' => [
                    'id' => 'forestpowerattackchance',
                    'min' => 0,
                    'max' => 100,
                    'value' => 0
                ],
                'options' => [
                    'label' => 'forest.forestpowerattackchance',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Multiplier for the power attack
        [
            'spec' => [
                'type' => 'range',
                'name' => 'forestpowerattackmulti',
                'attributes' => [
                    'id' => 'forestpowerattackmulti',
                    'min' => 1,
                    'max' => 10,
                    'value' => 1.5,
                    'step' => 0.1
                ],
                'options' => [
                    'label' => 'forest.forestpowerattackmulti',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Player will find a gem one in X times
        [
            'spec' => [
                'type' => 'range',
                'name' => 'forestgemchance',
                'attributes' => [
                    'id' => 'forestgemchance',
                    'min' => 10,
                    'max' => 100,
                    'value' => 10
                ],
                'options' => [
                    'label' => 'forest.forestgemchance',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Should monsters which get buffed with extra HP/Att/Def get a gold+exp bonus?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'disablebonuses',
                'attributes' => [
                    'id' => 'disablebonuses',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'forest.disablebonuses',
                ]
            ]
        ],
        // What percentage of experience should be lost?
        [
            'spec' => [
                'type' => 'range',
                'name' => 'forestexploss',
                'attributes' => [
                    'id' => 'forestexploss',
                    'min' => 10,
                    'max' => 100,
                    'value' => 10
                ],
                'options' => [
                    'label' => 'forest.forestexploss',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
    ]
];
