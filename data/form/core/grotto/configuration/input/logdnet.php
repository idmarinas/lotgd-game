<?php

return [
    'name' => 'logdnet',
    'attributes' => [
        'id' => 'logdnet'
    ],
    'options' => [
        'label' => 'logdnet.title'
    ],
    'elements' => [
        // Register with LoGDnet?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'logdnet',
                'attributes' => [
                    'id' => 'logdnet',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'logdnet.logdnet.label',
                    'note' => 'logdnet.logdnet.note'
                ]
            ]
        ],
        // Server Description (75 chars max)
        [
            'spec' => [
                'type' => 'text',
                'name' => 'serverdesc',
                'attributes' => [
                    'id' => 'serverdesc',
                ],
                'options' => [
                    'label' => 'logdnet.serverdesc'
                ]
            ]
        ],
        // Master LoGDnet Server (default http://logdnet.logd.com/)
        [
            'spec' => [
                'type' => 'text',
                'name' => 'logdnetserver',
                'attributes' => [
                    'id' => 'logdnetserver',
                ],
                'options' => [
                    'label' => 'logdnet.logdnetserver'
                ]
            ]
        ],
        // How long we wait for responses from that server (in seconds)
        [
            'spec' => [
                'type' => 'range',
                'name' => 'curltimeout',
                'attributes' => [
                    'id' => 'curltimeout',
                    'min' => 1,
                    'max' => 10,
                    'value' => 2
                ],
                'options' => [
                    'label' => 'logdnet.curltimeout'
                ]
            ]
        ],
    ]
];
