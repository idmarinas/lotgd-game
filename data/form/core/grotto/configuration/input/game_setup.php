<?php

return [
    'name' => 'game_setup',
    'attributes' => [
        'id' => 'game_setup',
    ],
    'options' => [
        'label' => 'game.setup.title'
    ],
    'elements' => [
        // Allow creation of new characters
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'allowcreation',
                'attributes' => [
                    'id' => 'allowcreation',
                    // This changes the checkbox in a Fomantic UI checkbox toggle. See https://fomantic-ui.com/modules/checkbox.html
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'game.setup.allow.creation',
                ]
            ]
        ],
        //- Name for the server
        [
            'spec' => [
                'type' => 'text',
                'name' => 'servername',
                'attributes' => [
                    'value' => 'The Legend of the Green Dragon',
                    'id' => 'servername',
                    'required' => true
                ],
                'options' => [
                    'label' => 'game.setup.server.name',
                    // This is optional, only need if want change for this element
                    // 'translator_text_domain' => 'form-core-grotto-configuration',
                ]
            ]
        ],
        // Server URL
        [
            'spec' => [
                'type' => 'text',
                'name' => 'serverurl',
                'attributes' => [
                    'id' => 'serverurl'
                ],
                'options' => [
                    'label' => 'game.setup.server.url'
                ]
            ]
        ],
        // Login Banner
        [
            'spec' => [
                'type' => 'text',
                'name' => 'loginbanner',
                'attributes' => [
                    'id' => 'loginbanner'
                ],
                'options' => [
                    'label' => 'game.setup.login.banner'
                ]
            ]
        ],
        // Max # of players online
        [
            'spec' => [
                'type' => 'number',
                'name' => 'maxonline',
                'attributes' => [
                    'id' => 'maxonline',
                    'value' => 0
                ],
                'options' => [
                    'label' => 'game.setup.max.online'
                ]
            ]
        ],
        // Admin Email
        [
            'spec' => [
                'type' => 'email',
                'name' => 'gameadminemail',
                'attributes' => [
                    'id' => 'gameadminemail'
                ],
                'options' => [
                    'label' => 'game.setup.game.admin.email'
                ]
            ]
        ],
        // Should submitted petitions be emailed to Admin Email address?
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'emailpetitions',
                'attributes' => [
                    'id' => 'emailpetitions',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'game.setup.email.petitions'
                ]
            ]
        ],
        // Languages actives on this server
        [
            'spec' => [
                'type' => 'ServerLanguage',
                'name' => 'serverlanguages',
                'attributes' => [
                    'id' => 'serverlanguages',
                    'multiple' => true,
                    'value' => ['en', 'fr', 'da', 'de', 'es', 'it'], // Default
                    'class' => 'fluid three column'

                ],
                'options' => [
                    'label' => 'game.setup.server.languages.label',
                    'empty_option' => 'game.setup.server.languages.empty'
                ]
            ]
        ],
        // Default Language
        [
            'spec' => [
                'type' => 'GameLanguage',
                'name' => 'defaultlanguage',
                'attributes' => [
                    'id' => 'defaultlanguage',
                    'required' => true,
                    'value' => 'en'
                ],
                'options' => [
                    'label' => 'game.setup.default.language',
                ]
            ]
        ],
        // What types can petitions be?
        [
            'spec' => [
                'type' => 'tags',
                'name' => 'petition_types',
                'attributes' => [
                    'id' => 'petition_types',
                    'required' => true,
                    'class' => 'tags-select',
                    'value' => 'petition.types.general,petition.types.report.bug,petition.types.suggestion,petition.types.commentpetition.types.other'
                ],
                'options' => [
                    'label' => 'game.setup.petition.types.label',
                    'note' => 'game.setup.petition.types.note',
                    // This is optional, only need if render element by element or for change
                    'note_translator_domain' => 'form-core-grotto-configuration'
                ]

            ]
        ],
        // Should DK titles be editable in user editor
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'edittitles',
                'attributes' => [
                    'id' => 'edittitles',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'game.setup.edit.titles',
                ]
            ]
        ],
        // How many items should be shown on the motdlist
        [
            'spec' => [
                'type' => 'number',
                'name' => 'motditems',
                'attributes' => [
                    'id' => 'motditems',
                    'required' => true,
                    'value' => 5
                ],
                'options' => [
                    'label' => 'game.setup.motd.items'
                ]
            ]
        ],
    ],
];
