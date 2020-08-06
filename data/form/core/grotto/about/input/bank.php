<?php

return [
    'name' => 'bank',
    'attributes' => [
        'id' => 'bank',
    ],
    'options' => [
        'label' => 'bank.title'
    ],
    'elements' => [
        //-- Max amount player can borrow per level
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'borrowperlevel',
                'attributes' => [
                    'id' => 'borrowperlevel'
                ],
                'options' => [
                    'label' => 'bank.borrowperlevel',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Are players allowed to transfer gold
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'allowgoldtransfer',
                'attributes' => [
                    'id' => 'allowgoldtransfer'
                ],
                'options' => [
                    'label' => 'bank.allowgoldtransfer',
                    'show_inline' => true,
                    'apply_filter' => 'yes_no'
                ]
            ]
        ],
        //-- Max amount player can transfer per level of recipient (if transfers are enabled)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'transferperlevel',
                'attributes' => [
                    'id' => 'transferperlevel'
                ],
                'options' => [
                    'label' => 'bank.transferperlevel',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Minimum level a player has to be before they can transfer gold (if transfers are enabled)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'mintransferlev',
                'attributes' => [
                    'id' => 'mintransferlev'
                ],
                'options' => [
                    'label' => 'bank.mintransferlev',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Total transfers a player can receive in one play day (if transfers are enabled)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'transferreceive',
                'attributes' => [
                    'id' => 'transferreceive'
                ],
                'options' => [
                    'label' => 'bank.transferreceive',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Max amount total a player can transfer to others per level (if transfers are enabled)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'maxtransferout',
                'attributes' => [
                    'id' => 'maxtransferout'
                ],
                'options' => [
                    'label' => 'bank.maxtransferout',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
    ],
];
