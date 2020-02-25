<?php

return [
    'name' => 'referral',
    'attributes' => [
        'id' => 'referral'
    ],
    'options' => [
        'label' => 'referral.title'
    ],
    'elements' => [
        [
            'spec' => [
                'type' => 'number',
                'name' => 'refereraward',
                'attributes' => [
                    'id' => 'refereraward',
                    'value' => 25
                ],
                'options' => [
                    'label' => 'referral.refereraward',
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'number',
                'name' => 'referminlevel',
                'attributes' => [
                    'id' => 'referminlevel',
                    'value' => 10
                ],
                'options' => [
                    'label' => 'referral.referminlevel',
                ]
            ]
        ],
    ]
];
