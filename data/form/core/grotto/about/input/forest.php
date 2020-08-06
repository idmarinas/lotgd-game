<?php

return [
    'name' => 'forest',
    'attributes' => [
        'id' => 'forest',
    ],
    'options' => [
        'label' => 'forest.title'
    ],
    'elements' => [
        //-- Forest Fights per day
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'turns',
                'attributes' => [
                    'id' => 'turns'
                ],
                'options' => [
                    'label' => 'forest.turns',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Forest Creatures always drop at least 1/4 of possible gold
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'dropmingold',
                'attributes' => [
                    'id' => 'dropmingold'
                ],
                'options' => [
                    'label' => 'forest.dropmingold',
                    'show_inline' => true,
                    'apply_filter' => 'yes_no'
                ]
            ]
        ],
    ],
];
