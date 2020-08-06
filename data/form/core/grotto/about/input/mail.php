<?php

return [
    'name' => 'mail',
    'attributes' => [
        'id' => 'mail',
    ],
    'options' => [
        'label' => 'mail.title'
    ],
    'elements' => [
        //-- Message size limit per message
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'mailsizelimit',
                'attributes' => [
                    'id' => 'mailsizelimit'
                ],
                'options' => [
                    'label' => 'mail.mailsizelimit',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Limit # of messages in inbox,viewonly
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'inboxlimit',
                'attributes' => [
                    'id' => 'inboxlimit'
                ],
                'options' => [
                    'label' => 'mail.inboxlimit',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
        //-- Automatically delete old messages after (days)
        [
            'spec' => [
                'type' => 'viewonly',
                'name' => 'oldmail',
                'attributes' => [
                    'id' => 'oldmail'
                ],
                'options' => [
                    'label' => 'mail.oldmail',
                    'show_inline' => true,
                    'apply_filter' => 'numeral'
                ]
            ]
        ],
    ],
];
