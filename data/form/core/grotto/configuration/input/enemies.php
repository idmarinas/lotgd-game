<?php

return [
    'name' => 'enemies',
    'attributes' => [
        'id' => 'enemies'
    ],
    'options' => [
        'label' => 'enemies.title'
    ],
    'elements' => [
        // Multiple monsters will attack players above which amount of dragonkills?
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multifightdk',
                'attributes' => [
                    'id' => 'multifightdk',
                    'min' => 8,
                    'max' => 50,
                    'value' => 8
                ],
                'options' => [
                    'label' => 'enemies.multifightdk'
                ]
            ]
        ],
        // The chance for an attack from multiple enemies is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multichance',
                'attributes' => [
                    'id' => 'multichance',
                    'min' => 0,
                    'max' => 100,
                    'value' => 30
                ],
                'options' => [
                    'label' => 'enemies.multichance',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Can one creature in the creature table appear in a pack (all monsters you encounter in that fight are duplicates of this?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'allowpackmonsters',
                'attributes' => [
                    'id' => 'allowpackmonsters',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'enemies.allowpackmonsters',
                ]
            ]
        ],
        // Need Multiple Enemies to be from a different category (sanity reasons)?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'multicategory',
                'attributes' => [
                    'id' => 'multicategory',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'enemies.multicategory',
                ]
            ]
        ],
        // Additional experience (%) per enemy during multifights?
        [
            'spec' => [
                'type' => 'range',
                'name' => 'addexp',
                'attributes' => [
                    'id' => 'addexp',
                    'min' => 0,
                    'max' => 15,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'enemies.addexp',
                ]
            ]
        ],
        // How many enemies will attack per round (max. value)
        [
            'spec' => [
                'type' => 'range',
                'name' => 'maxattacks',
                'attributes' => [
                    'id' => 'maxattacks',
                    'min' => 1,
                    'max' => 15,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'enemies.maxattacks.label',
                    'note' => 'enemies.maxattacks.note',
                ]
            ]
        ],
        // During multi-fights hand out experience instantly?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'instantexp',
                'attributes' => [
                    'id' => 'instantexp',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'enemies.instantexp',
                ]
            ]
        ],
        // The base number of multiple enemies at minimum is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multibasemin',
                'attributes' => [
                    'id' => 'multibasemin',
                    'min' => 0,
                    'max' => 50,
                    'value' => 2
                ],
                'options' => [
                    'label' => 'enemies.multibasemin',
                ]
            ]
        ],
        // The base number of multiple enemies at maximum is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multibasemax',
                'attributes' => [
                    'id' => 'multibasemax',
                    'min' => 0,
                    'max' => 50,
                    'value' => 3
                ],
                'options' => [
                    'label' => 'enemies.multibasemax',
                ]
            ]
        ],
        // The number of multiple enemies at minimum for slumming is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multislummin',
                'attributes' => [
                    'id' => 'multislummin',
                    'min' => 0,
                    'max' => 50,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'enemies.multislummin',
                ]
            ]
        ],
        // The number of multiple enemies at maximum for slumming is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multislummax',
                'attributes' => [
                    'id' => 'multislummax',
                    'min' => 0,
                    'max' => 50,
                    'value' => 2
                ],
                'options' => [
                    'label' => 'enemies.multislummax',
                ]
            ]
        ],
        // The number of multiple enemies at minimum for thrill seeking is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multithrillmin',
                'attributes' => [
                    'id' => 'multithrillmin',
                    'min' => 0,
                    'max' => 50,
                    'value' => 3
                ],
                'options' => [
                    'label' => 'enemies.multithrillmin',
                ]
            ]
        ],
        // The number of multiple enemies at maximum for thrill seeking is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multithrillmax',
                'attributes' => [
                    'id' => 'multithrillmax',
                    'min' => 0,
                    'max' => 50,
                    'value' => 4
                ],
                'options' => [
                    'label' => 'enemies.multithrillmax',
                ]
            ]
        ],
        // The number of multiple enemies at minimum for suicide is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multisuimin',
                'attributes' => [
                    'id' => 'multisuimin',
                    'min' => 0,
                    'max' => 50,
                    'value' => 4
                ],
                'options' => [
                    'label' => 'enemies.multisuimin',
                ]
            ]
        ],
        // The number of multiple enemies at maximum for suicide is
        [
            'spec' => [
                'type' => 'range',
                'name' => 'multisuimax',
                'attributes' => [
                    'id' => 'multisuimax',
                    'min' => 0,
                    'max' => 50,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'enemies.multisuimax',
                ]
            ]
        ],
    ]
];
