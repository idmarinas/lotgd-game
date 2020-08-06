<?php

return [
    'name' => 'content',
    'attributes' => [
        'id' => 'content',
    ],
    'options' => [
        'label' => 'content.title'
    ],
    'elements' => [
        //-- Days to keep comments and news?  (0 for infinite)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'expirecontent',
                'attributes' => [
                    'id' => 'expirecontent'
                ],
                'options' => [
                    'label' => 'content.expirecontent',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Days to keep accounts that were never logged in to? (0 for infinite)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'expiretrashacct',
                'attributes' => [
                    'id' => 'expiretrashacct'
                ],
                'options' => [
                    'label' => 'content.expiretrashacct',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Days to keep level 1 accounts with no dragon kills? (0 for infinite)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'expirenewacct',
                'attributes' => [
                    'id' => 'expirenewacct'
                ],
                'options' => [
                    'label' => 'content.expirenewacct',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Days to keep all other accounts? (0 for infinite)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'expireoldacct',
                'attributes' => [
                    'id' => 'expireoldacct'
                ],
                'options' => [
                    'label' => 'content.expireoldacct',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Seconds of inactivity before auto-logoff
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'LOGINTIMEOUT',
                'attributes' => [
                    'id' => 'LOGINTIMEOUT'
                ],
                'options' => [
                    'label' => 'content.LOGINTIMEOUT',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
    ],
];
