<?php

return [
    'name' => 'mail',
    'attributes' => [
        'id' => 'mail'
    ],
    'options' => [
        'label' => 'mail.title'
    ],
    'elements' => [
        // Message size limit per message
        [
            'spec' => [
                'type' => 'number',
                'name' => 'mailsizelimit',
                'attributes' => [
                    'id' => 'mailsizelimit',
                    'value' => 1024
                ],
                'options' => [
                    'label' => 'mail.mailsizelimit',
                ]
            ]
        ],
        // Limit # of messages in inbox
        [
            'spec' => [
                'type' => 'number',
                'name' => 'inboxlimit',
                'attributes' => [
                    'id' => 'inboxlimit',
                    'value' => 50
                ],
                'options' => [
                    'label' => 'mail.inboxlimit',
                ]
            ]
        ],
        // Automatically delete old messages after (days)
        [
            'spec' => [
                'type' => 'number',
                'name' => 'oldmail',
                'attributes' => [
                    'id' => 'oldmail',
                    'value' => 30
                ],
                'options' => [
                    'label' => 'mail.oldmail',
                ]
            ]
        ],
        // Warning to give when attempting to YoM an admin?
        [
            'spec' => [
                'type' => 'textarea',
                'name' => 'superuseryommessage',
                'attributes' => [
                    'id' => 'superuseryommessage',
                ],
                'options' => [
                    'label' => 'mail.superuseryommessage',
                ]
            ]
        ],
        // Only unread mail count towards the inbox limit?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'onlyunreadmails',
                'attributes' => [
                    'id' => 'onlyunreadmails',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'mail.onlyunreadmails',
                ]
            ]
        ],
    ]
];
