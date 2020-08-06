<?php

return [
    'name' => 'newday',
    'attributes' => [
        'id' => 'newday',
    ],
    'options' => [
        'label' => 'newday.title'
    ],
    'elements' => [
        //-- Player must have fewer than how many forest fights to earn interest?
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'fightsforinterest',
                'attributes' => [
                    'id' => 'fightsforinterest'
                ],
                'options' => [
                    'label' => 'newday.fightsforinterest',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Max Interest Rate (%)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'maxinterest',
                'attributes' => [
                    'id' => 'maxinterest'
                ],
                'options' => [
                    'label' => 'newday.maxinterest',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Min Interest Rate (%)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'mininterest',
                'attributes' => [
                    'id' => 'mininterest'
                ],
                'options' => [
                    'label' => 'newday.mininterest',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Game days per calendar day
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'daysperday',
                'attributes' => [
                    'id' => 'daysperday'
                ],
                'options' => [
                    'label' => 'newday.daysperday',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Extra daily uses in specialty area
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'specialtybonus',
                'attributes' => [
                    'id' => 'specialtybonus'
                ],
                'options' => [
                    'label' => 'newday.specialtybonus',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
    ],
];
