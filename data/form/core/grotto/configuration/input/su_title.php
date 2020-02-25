<?php

return [
    'name' => 'su_title',
    'attributes' => [
        'id' => 'su_title'
    ],
    'options' => [
        'label' => 'su.title.title'
    ],
    'elements' => [
        // Enable chat tags in general
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'enable_chat_tags',
                'attributes' => [
                    'id' => 'enable_chat_tags',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'su.title.enable_chat_tags.label',
                    'note' => 'su.title.enable_chat_tags.note'
                ]
            ]
        ],
        // Title for the mega user
        [
            'spec' => [
                'type' => 'text',
                'name' => 'chat_tag_megauser',
                'attributes' => [
                    'id' => 'chat_tag_megauser',
                ],
                'options' => [
                    'label' => 'su.title.chat_tag_megauser',
                ]
            ]
        ],
        // Name for a GM
        [
            'spec' => [
                'type' => 'text',
                'name' => 'chat_tag_gm',
                'attributes' => [
                    'id' => 'chat_tag_gm',
                ],
                'options' => [
                    'label' => 'su.title.chat_tag_gm',
                ]
            ]
        ],
        // Name for a Mod
        [
            'spec' => [
                'type' => 'text',
                'name' => 'chat_tag_mod',
                'attributes' => [
                    'id' => 'chat_tag_mod'
                ],
                'options' => [
                    'label' => 'su.title.chat_tag_mod',
                ]
            ]
        ],
    ]
];
