<?php

return [
    'name' => 'bank',
    'attributes' => [
        'id' => 'bank'
    ],
    'options' => [
        'label' => 'bank.title'
    ],
    'elements' => [
        // Max forest fights remaining to earn interest?
        [
            'spec' => [
                'type' => 'range',
                'name' => 'fightsforinterest',
                'attributes' => [
                    'id' => 'fightsforinterest',
                    'min' => 0,
                    'max' => 10,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'bank.fightsforinterest'
                ]
            ]
        ],
        // Max Interest Rate (%)
        [
            'spec' => [
                'type' => 'range',
                'name' => 'maxinterest',
                'attributes' => [
                    'id' => 'maxinterest',
                    'min' => 5,
                    'max' => 10,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'bank.maxinterest'
                ]
            ]
        ],
        // Min Interest Rate (%)
        [
            'spec' => [
                'type' => 'range',
                'name' => 'mininterest',
                'attributes' => [
                    'id' => 'mininterest',
                    'min' => 0,
                    'max' => 5,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'bank.mininterest'
                ]
            ]
        ],
        // Over what amount of gold does the bank cease paying interest?
        [
            'spec' => [
                'type' => 'number',
                'name' => 'maxgoldforinterest',
                'attributes' => [
                    'id' => 'maxgoldforinterest',
                    'value' => 100000
                ],
                'options' => [
                    'label' => 'bank.maxgoldforinterest'
                ]
            ]
        ],
        // Max player can borrow per level (val * level for max)
        [
            'spec' => [
                'type' => 'range',
                'name' => 'borrowperlevel',
                'attributes' => [
                    'id' => 'borrowperlevel',
                    'min' => 5,
                    'max' => 200,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'bank.borrowperlevel',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Allow players to transfer gold
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'allowgoldtransfer',
                'attributes' => [
                    'id' => 'allowgoldtransfer',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'bank.allowgoldtransfer'
                ]
            ]
        ],
        // Max player can receive from a transfer (val * level)
        [
            'spec' => [
                'type' => 'range',
                'name' => 'transferperlevel',
                'attributes' => [
                    'id' => 'transferperlevel',
                    'min' => 5,
                    'max' => 100,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'bank.transferperlevel',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Min level a player (0 DK's) needs to transfer gold
        [
            'spec' => [
                'type' => 'range',
                'name' => 'mintransferlev',
                'attributes' => [
                    'id' => 'mintransferlev',
                    'min' => 1,
                    'max' => 5,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'bank.mintransferlev',
                    // 'disable_slider_labels' => true,
                ]
            ]
        ],
        // Total transfers a player can receive in one day
        [
            'spec' => [
                'type' => 'range',
                'name' => 'transferreceive',
                'attributes' => [
                    'id' => 'transferreceive',
                    'min' => 0,
                    'max' => 5,
                    'value' => 1
                ],
                'options' => [
                    'label' => 'bank.transferreceive',
                    // 'disable_slider_labels' => true,
                ]
            ]
        ],
        // Amount player can transfer to others (val * level)
        [
            'spec' => [
                'type' => 'range',
                'name' => 'maxtransferout',
                'attributes' => [
                    'id' => 'maxtransferout',
                    'min' => 5,
                    'max' => 100,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'bank.maxtransferout',
                    'disable_slider_labels' => true,
                ]
            ]
        ],
        // Fee for express inn payment (x or x%)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'innfee',
                'attributes' => [
                    'id' => 'innfee',
                    'value' => 0
                ],
                'options' => [
                    'label' => 'bank.innfee'
                ]
            ]
        ],
    ]
];
