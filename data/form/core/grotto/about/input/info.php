<?php

return [
    'name' => 'info',
    'attributes' => [
        'id' => 'info',
    ],
    'options' => [
        'label' => 'info.title'
    ],
    'elements' => [
        //-- Day Duration
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'dayduration',
                'attributes' => [
                    'id' => 'dayduration'
                ],
                'options' => [
                    'label' => 'info.dayduration',
                    'show_inline' => true
                ]
            ]
        ],
        //-- Current game time
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'curgametime',
                'attributes' => [
                    'id' => 'curgametime'
                ],
                'options' => [
                    'label' => 'info.curgametime',
                    'show_inline' => true
                ]
            ]
        ],
        //-- Current Server Time
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'curservertime',
                'attributes' => [
                    'id' => 'curservertime'
                ],
                'options' => [
                    'label' => 'info.curservertime',
                    'show_inline' => true
                ]
            ]
        ],
        //-- Last new day
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'lastnewday',
                'attributes' => [
                    'id' => 'lastnewday'
                ],
                'options' => [
                    'label' => 'info.lastnewday',
                    'show_inline' => true
                ]
            ]
        ],
        //-- Next new day
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'nextnewday',
                'attributes' => [
                    'id' => 'nextnewday'
                ],
                'options' => [
                    'label' => 'info.nextnewday',
                    'show_inline' => true
                ]
            ]
        ],
    ],
];
