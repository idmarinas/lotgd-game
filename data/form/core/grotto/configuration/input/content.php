<?php

return [
    'name' => 'content',
    'attributes' => [
        'id' => 'content'
    ],
    'options' => [
        'label' => 'content.title'
    ],
    'elements' => [
        // Days to keep comments and news?  (0 = infinite)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'expirecontent',
                'attributes' => [
                    'id' => 'expirecontent',
                ],
                'options' => [
                    'label' => 'content.expirecontent',
                ]
            ]
        ],
        // Days to keep the debuglog? (0=infinite)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'expiredebuglog',
                'attributes' => [
                    'id' => 'expiredebuglog',
                ],
                'options' => [
                    'label' => 'content.expiredebuglog',
                ]
            ]
        ],
        // Days to keep the faillog? (0=infinite)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'expirefaillog',
                'attributes' => [
                    'id' => 'expirefaillog',
                ],
                'options' => [
                    'label' => 'content.expirefaillog',
                ]
            ]
        ],
        // Days to keep the gamelog? (0=infinite)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'expiregamelog',
                'attributes' => [
                    'id' => 'expiregamelog',
                ],
                'options' => [
                    'label' => 'content.expiregamelog',
                ]
            ]
        ],
        // Days to keep never logged-in accounts? (0 = infinite)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'expiretrashacct',
                'attributes' => [
                    'id' => 'expiretrashacct',
                ],
                'options' => [
                    'label' => 'content.expiretrashacct',
                ]
            ]
        ],
        // Days to keep 1 level (0 dragon) accounts? (0 =infinite)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'expirenewacct',
                'attributes' => [
                    'id' => 'expirenewacct',
                ],
                'options' => [
                    'label' => 'content.expirenewacct',
                ]
            ]
        ],
        // Notify the user how many days before expiration via email
        [
            'spec' => [
                'type' => 'number',
                'name' => 'expirenotificationdays',
                'attributes' => [
                    'id' => 'expirenotificationdays',
                    'value' => 3
                ],
                'options' => [
                    'label' => 'content.expirenotificationdays.label',
                    'note' => 'content.expirenotificationdays.note',
                ]
            ]
        ],
        // Days to keep all other accounts? (0 = infinite)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'expireoldacct',
                'attributes' => [
                    'id' => 'expireoldacct',
                ],
                'options' => [
                    'label' => 'content.expireoldacct',
                ]
            ]
        ],
        // Seconds of inactivity before auto-logoff
        [
            'spec' => [
                'type' => 'number',
                'name' => 'LOGINTIMEOUT',
                'attributes' => [
                    'id' => 'LOGINTIMEOUT',
                ],
                'options' => [
                    'label' => 'content.LOGINTIMEOUT',
                ]
            ]
        ],
    ]
];
