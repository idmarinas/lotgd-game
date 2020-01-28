<?php

return [
    'attributes' => [
        'method' => 'post',
        'action' => 'configuration.php?setting=default&save=save',
        'autocomplete' => false,
        'class' => 'ui form',
        'name' => 'data-setup',
        'id' => 'data-setup'
    ],
    'options' => [
        'label' => 'form.label',
        'translator_text_domain' => 'form-core-grotto-configuration', // This is necesary for translate all labels of form
        // Csrf element (to prevent Cross Site Request Forgery attacks)
        // It is extremely recommended to add this element in all forms
        'use_csrf_security' => true, // true|false - Default value is true
        /**
         * Buttons for form, this key can be a bool value or an array.
         *
         * If this key is not present in the form, no button is displayed
         *
         * Can use default buttons or customize in array options for Zend form factory element
         * Can add more buttons if need
         *
         * Order of render submit, reset and others
         */
        'buttons' => [
            'submit' => true, // true|false - Default value is true
            'reset' => false, // true|false - Default value is false
            // Example of custom button
            // 'example' => [
            //     'name' => 'example',
            //     'type' => 'button',
            //     'attributes' => [
            //         'id' => 'button',
            //         'class' => 'ui button'
            //     ],
            //     'options' => [
            //         'label' => 'button.example',
            //         'translator_text_domain' => 'app-form'
            //     ]
            // ]
        ],
        // This will add the default buttons.
        // 'buttons' => true,
    ],
    'fieldsets' => [
        [
            // Game Setup
            'spec' => [
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
            ],
        ],
        [
            // Maintenance
            'spec' => [
                'name' => 'maintenance',
                'attributes' => [
                    'id' => 'maintenance'
                ],
                'options' => [
                    'label' => 'maintenance.title'
                ],
                'elements' => [
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'debug',
                            'attributes' => [
                                'id' => 'debug',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'maintenance.debug.label',
                                'note' => 'maintenance.debug.note',
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'maintenance',
                            'attributes' => [
                                'id' => 'maintenance',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'maintenance.maintenance.label',
                                'note' => 'maintenance.maintenance.note',
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'fullmaintenance',
                            'attributes' => [
                                'id' => 'fullmaintenance',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'maintenance.fullmaintenance.label',
                                'note' => 'maintenance.fullmaintenance.note',
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'textarea',
                            'name' => 'maintenancenote',
                            'attributes' => [
                                'id' => 'maintenancenote'
                            ],
                            'options' => [
                                'label' => 'maintenance.maintenancenote'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'textarea',
                            'name' => 'maintenanceauthor',
                            'attributes' => [
                                'id' => 'maintenanceauthor'
                            ],
                            'options' => [
                                'label' => 'maintenance.maintenanceauthor'
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Main Page
            'spec' => [
                'name' => 'home',
                'attributes' => [
                    'id' => 'home'
                ],
                'options' => [
                    'label' => 'home.title'
                ],
                'elements' => [
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'homeskinselect',
                            'attributes' => [
                                'id' => 'homeskinselect',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'home.homeskinselect'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'homecurtime',
                            'attributes' => [
                                'id' => 'homecurtime',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'home.homecurtime'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'homenewdaytime',
                            'attributes' => [
                                'id' => 'homenewdaytime',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'home.homenewdaytime'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'homenewestplayer',
                            'attributes' => [
                                'id' => 'homenewestplayer',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'home.homenewestplayer'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'lotgdTheme',
                            'name' => 'defaultskin',
                            'attributes' => [
                                'id' => 'defaultskin',
                            ],
                            'options' => [
                                'label' => 'home.defaultskin'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'textarea',
                            'name' => 'impressum',
                            'attributes' => [
                                'id' => 'impressum',
                            ],
                            'options' => [
                                'label' => 'home.impressum'
                            ]
                        ]
                    ],
                ]
            ],
        ],
        [
            // Beta
            'spec' => [
                'name' => 'beta',
                'attributes' => [
                    'id' => 'beta'
                ],
                'options' => [
                    'label' => 'beta.title'
                ],
                'elements' => [
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'beta',
                            'attributes' => [
                                'id' => 'beta',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'beta.beta'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'betaperplayer',
                            'attributes' => [
                                'id' => 'betaperplayer',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'beta.betaperplayer.label',
                                'note' => 'beta.betaperplayer.note',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Account Creation
            'spec' => [
                'name' => 'account',
                'attributes' => [
                    'id' => 'account'
                ],
                'options' => [
                    'label' => 'account.title'
                ],
                'elements' => [
                    [
                        'spec' => [
                            'type' => 'bitfield',
                            'name' => 'defaultsuperuser',
                            'attributes' => [
                                'id' => 'defaultsuperuser',
                            ],
                            'options' => [
                                'label' => 'account.defaultsuperuser.label',
                                'value_options' => [
                                    SU_INFINITE_DAYS => 'account.defaultsuperuser.options.infinite.days',
                                    SU_VIEW_SOURCE => 'account.defaultsuperuser.options.view.source',
                                    SU_DEVELOPER => 'account.defaultsuperuser.options.developer',
                                    SU_DEBUG_OUTPUT => 'account.defaultsuperuser.options.debug.output'
                                ]
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'newplayerstartgold',
                            'attributes' => [
                                'id' => 'newplayerstartgold'
                            ],
                            'options' => [
                                'label' => 'account.newplayerstartgold'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'maxrestartgold',
                            'attributes' => [
                                'id' => 'maxrestartgold'
                            ],
                            'options' => [
                                'label' => 'account.maxrestartgold'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'maxrestartgems',
                            'attributes' => [
                                'id' => 'maxrestartgems'
                            ],
                            'options' => [
                                'label' => 'account.maxrestartgems'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'playerchangeemail',
                            'attributes' => [
                                'id' => 'playerchangeemail',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'account.playerchangeemail'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'playerchangeemailauto',
                            'attributes' => [
                                'id' => 'playerchangeemailauto',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'account.playerchangeemailauto.label',
                                'note' => 'account.playerchangeemailauto.note',
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'playerchangeemaildays',
                            'attributes' => [
                                'id' => 'playerchangeemaildays',
                                'min' => 1,
                                'max' => 30,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'account.playerchangeemaildays',
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'select',
                            'name' => 'validationtarget',
                            'attributes' => [
                                'id' => 'validationtarget',
                            ],
                            'options' => [
                                'label' => 'account.validationtarget.label',
                                'value_options' => [
                                    0 => 'account.validationtarget.options.infinite.old',
                                    1 => 'account.validationtarget.options.view.new',
                                ],
                                'note' => 'account.validationtarget.note'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'requireemail',
                            'attributes' => [
                                'id' => 'requireemail',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'account.requireemail'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'requirevalidemail',
                            'attributes' => [
                                'id' => 'requirevalidemail',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'account.requirevalidemail'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'blockdupeemail',
                            'attributes' => [
                                'id' => 'blockdupeemail',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'account.blockdupeemail'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'spaceinname',
                            'attributes' => [
                                'id' => 'spaceinname',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'account.spaceinname'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'allowoddadminrenames',
                            'attributes' => [
                                'id' => 'allowoddadminrenames',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'account.allowoddadminrenames'
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'selfdelete',
                            'attributes' => [
                                'id' => 'selfdelete',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'account.selfdelete'
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Commentary/Chat
            'spec' => [
                'name' => 'commentary',
                'attributes' => [
                    'id' => 'commentary'
                ],
                'options' => [
                    'label' => 'commentary.title'
                ],
                'elements' => [
                    // Clean user posts (filters bad language and splits words over 45 chars long)
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'soap',
                            'attributes' => [
                                'id' => 'soap',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'commentary.soap'
                            ]
                        ]
                    ],
                    // Max # of color changes usable in one comment
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'maxcolors',
                            'attributes' => [
                                'id' => 'maxcolors',
                                'min' => 5,
                                'max' => 40,
                                'value' => 5
                            ],
                            'options' => [
                                'label' => 'commentary.maxcolors',
                            ]
                        ]
                    ],
                    // Limit posts to let one user post only up to 50% of the last posts (else turn it off)
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'postinglimit',
                            'attributes' => [
                                'id' => 'postinglimit',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'commentary.postinglimit'
                            ]
                        ]
                    ],
                    // Length of the chatline in chars
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'chatlinelength',
                            'attributes' => [
                                'id' => 'chatlinelength',
                                'min' => 5,
                                'max' => 1000,
                                'value' => 40
                            ],
                            'options' => [
                                'label' => 'commentary.chatlinelength',
                            ]
                        ]
                    ],
                    // Number of maximum chars for a single chat line
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'maxchars',
                            'attributes' => [
                                'id' => 'maxchars',
                                'min' => 50,
                                'max' => 1000,
                                'value' => 50
                            ],
                            'options' => [
                                'label' => 'commentary.maxchars',
                            ]
                        ]
                    ],
                    // Sections to exclude from comment moderation
                    [
                        'spec' => [
                            'type' => 'textarea',
                            'name' => 'moderateexcludes',
                            'attributes' => [
                                'id' => 'moderateexcludes'
                            ],
                            'options' => [
                                'label' => 'commentary.moderateexcludes'
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            // Place names and People names
            'spec' => [
                'name' => 'places',
                'attributes' => [
                    'id' => 'places'
                ],
                'options' => [
                    'label' => 'places.title'
                ],
                'elements' => [
                    // Name for the main village
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'villagename',
                            'attributes' => [
                                'id' => 'villagename',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'places.villagename',
                            ]
                        ]
                    ],
                    // Name of the inn
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'innname',
                            'attributes' => [
                                'id' => 'innname',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'places.innname',
                            ]
                        ]
                    ],
                    // Name of the barkeep
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'barkeep',
                            'attributes' => [
                                'id' => 'barkeep',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'places.barkeep',
                            ]
                        ]
                    ],
                    // Name of the barmaid
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'barmaid',
                            'attributes' => [
                                'id' => 'barmaid',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'places.barmaid',
                            ]
                        ]
                    ],
                    // Name of the bard
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'bard',
                            'attributes' => [
                                'id' => 'bard',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'places.bard',
                            ]
                        ]
                    ],
                    // Name of the clan registrar
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'clanregistrar',
                            'attributes' => [
                                'id' => 'clanregistrar',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'places.clanregistrar',
                            ]
                        ]
                    ],
                    // Name of the banker
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'bankername',
                            'attributes' => [
                                'id' => 'bankername',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'places.bankername',
                            ]
                        ]
                    ],
                    // Name of the death overlord
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'deathoverlord',
                            'attributes' => [
                                'id' => 'deathoverlord',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'places.deathoverlord',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // SU titles
            'spec' => [
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
                                'required' => true
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
                                'required' => true
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
                                'id' => 'chat_tag_mod',
                                'required' => true
                            ],
                            'options' => [
                                'label' => 'su.title.chat_tag_mod',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Referral Settings
            'spec' => [
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
                                'id' => 'refereraward'
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
                                'id' => 'referminlevel'
                            ],
                            'options' => [
                                'label' => 'referral.referminlevel',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Random events
            'spec' => [
                'name' => 'events',
                'attributes' => [
                    'id' => 'events'
                ],
                'options' => [
                    'label' => 'events.title'
                ],
                'elements' => [
                    // Chance for Something Special in the Forest
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'forestchance',
                            'attributes' => [
                                'id' => 'forestchance',
                                'min' => 0,
                                'max' => 100,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'events.forestchance',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Chance for Something Special in any village
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'villagechance',
                            'attributes' => [
                                'id' => 'villagechance',
                                'min' => 0,
                                'max' => 100,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'events.villagechance',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Chance for Something Special in the Inn
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'innchance',
                            'attributes' => [
                                'id' => 'innchance',
                                'min' => 0,
                                'max' => 100,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'events.innchance',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Chance for Something Special in the Graveyard
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'gravechance',
                            'attributes' => [
                                'id' => 'gravechance',
                                'min' => 0,
                                'max' => 100,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'events.gravechance',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Chance for Something Special in the Gardens
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'gardenchance',
                            'attributes' => [
                                'id' => 'gardenchance',
                                'min' => 0,
                                'max' => 100,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'events.gardenchance',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Paypal and Donations
            'spec' => [
                'name' => 'donation',
                'attributes' => [
                    'id' => 'donation'
                ],
                'options' => [
                    'label' => 'donation.title'
                ],
                'elements' => [
                    // Points to award for $1 (or 1 of whatever currency you allow players to donate)
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'dpointspercurrencyunit',
                            'attributes' => [
                                'id' => 'dpointspercurrencyunit'
                            ],
                            'options' => [
                                'label' => 'donation.dpointspercurrencyunit',
                            ]
                        ]
                    ],
                    // Email address of Admin's paypal account
                    [
                        'spec' => [
                            'type' => 'email',
                            'name' => 'paypalemail',
                            'attributes' => [
                                'id' => 'paypalemail'
                            ],
                            'options' => [
                                'label' => 'donation.paypalemail',
                            ]
                        ]
                    ],
                    // Currency type
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'paypalcurrency',
                            'attributes' => [
                                'id' => 'paypalcurrency'
                            ],
                            'options' => [
                                'label' => 'donation.paypalcurrency',
                            ]
                        ]
                    ],
                    // What country's predominant language do you wish to have displayed in your PayPal screen?
                    [
                        'spec' => [
                            'type' => 'select',
                            'name' => 'paypalcountry-code',
                            'attributes' => [
                                'id' => 'paypalcountry-code'
                            ],
                            'options' => [
                                'label' => 'donation.paypalcountry.code',
                                'value_options' => [
                                    'US' => 'United States',
                                    'DE' => 'Germany',
                                    'AI' => 'Anguilla',
                                    'AR' => 'Argentina',
                                    'AU' => 'Australia',
                                    'AT' => 'Austria',
                                    'BE' => 'Belgium',
                                    'BR' => 'Brazil',
                                    'CA' => 'Canada',
                                    'CL' => 'Chile',
                                    'C2' => 'China',
                                    'CR' => 'Costa Rica',
                                    'CY' => 'Cyprus',
                                    'CZ' => 'Czech Republic',
                                    'DK' => 'Denmark',
                                    'DO' => 'Dominican Republic',
                                    'EC' => 'Ecuador',
                                    'EE' => 'Estonia',
                                    'FI' => 'Finland',
                                    'FR' => 'France',
                                    'GR' => 'Greece',
                                    'HK' => 'Hong Kong',
                                    'HU' => 'Hungary',
                                    'IS' => 'Iceland',
                                    'IN' => 'India',
                                    'IE' => 'Ireland',
                                    'IL' => 'Israel',
                                    'IT' => 'Italy',
                                    'JM' => 'Jamaica',
                                    'JP' => 'Japan',
                                    'LV' => 'Latvia',
                                    'LT' => 'Lithuania',
                                    'LU' => 'Luxembourg',
                                    'MY' => 'Malaysia',
                                    'MT' => 'Malta',
                                    'MX' => 'Mexico',
                                    'NL' => 'Netherlands',
                                    'NZ' => 'New Zealand',
                                    'NO' => 'Norway',
                                    'PL' => 'Poland',
                                    'PT' => 'Portugal',
                                    'SG' => 'Singapore',
                                    'SK' => 'Slovakia',
                                    'SI' => 'Slovenia',
                                    'ZA' => 'South Africa',
                                    'KR' => 'South Korea',
                                    'ES' => 'Spain',
                                    'SE' => 'Sweden',
                                    'CH' => 'Switzerland',
                                    'TW' => 'Taiwan',
                                    'TH' => 'Thailand',
                                    'TR' => 'Turkey',
                                    'GB' => 'United Kingdom',
                                    'UY' => 'Uruguay',
                                    'VE' => 'Venezuela'
                                ],
                            ]
                        ]
                    ],
                    // What text should be displayed as item name in the donations screen(player name will be added after it)?
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'paypaltext',
                            'attributes' => [
                                'id' => 'paypaltext',
                                'value' => 'Legend of the Green Dragon Site Donation from'
                            ],
                            'options' => [
                                'label' => 'donation.paypaltext.label',
                                'note' => 'donation.paypaltext.note',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // General Combat
            'spec' => [
                'name' => 'combat',
                'attributes' => [
                    'id' => 'combat'
                ],
                'options' => [
                    'label' => 'combat.title'
                ],
                'elements' => [
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'autofight',
                            'attributes' => [
                                'id' => 'autofight',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'combat.autofight',
                            ]
                        ]
                    ],
                    [
                        'spec' => [
                            'type' => 'select',
                            'name' => 'autofightfull',
                            'attributes' => [
                                'id' => 'autofightfull',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'combat.autofightfull.label',
                                'value_options' => [
                                    0 => 'combat.autofightfull.option.never',
                                    1 => 'combat.autofightfull.option.always',
                                    2 => 'combat.autofightfull.option.flee',
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Training & Levelling
            'spec' => [
                'name' => 'training',
                'attributes' => [
                    'id' => 'training'
                ],
                'options' => [
                    'label' => 'training.title'
                ],
                'elements' => [
                    // Masters hunt down truant students
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'automaster',
                            'attributes' => [
                                'id' => 'automaster',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'training.automaster',
                            ]
                        ]
                    ],
                    // Can players gain multiple levels (challenge multiple masters) per game day?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'multimaster',
                            'attributes' => [
                                'id' => 'multimaster',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'training.multimaster',
                            ]
                        ]
                    ],
                    // Display news if somebody fought his master?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'displaymasternews',
                            'attributes' => [
                                'id' => 'displaymasternews',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'training.displaymasternews.label',
                                'note' => 'training.displaymasternews.note'
                            ]
                        ]
                    ],
                    // Which is the maximum attainable level (at which also the Dragon shows up)?
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'maxlevel',
                            'attributes' => [
                                'id' => 'maxlevel',
                            ],
                            'options' => [
                                'label' => 'training.maxlevel.label',
                                'note' => 'training.maxlevel.note'
                            ]
                        ]
                    ],
                    // Give here what experience is necessary for each level
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'exp-array',
                            'attributes' => [
                                'id' => 'exp-array',
                            ],
                            'options' => [
                                'label' => 'training.exp.array.label',
                                'note' => 'training.exp.array.note'
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Clans
            'spec' => [
                'name' => 'clans',
                'attributes' => [
                    'id' => 'clans'
                ],
                'options' => [
                    'label' => 'clans.title'
                ],
                'elements' => [
                    // Enable Clan System?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'allowclans',
                            'attributes' => [
                                'id' => 'allowclans',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'clans.allowclans',
                            ]
                        ]
                    ],
                    // Gold to start a clan
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'goldtostartclan',
                            'attributes' => [
                                'id' => 'goldtostartclan',
                            ],
                            'options' => [
                                'label' => 'clans.goldtostartclan',
                            ]
                        ]
                    ],
                    // Gems to start a clan
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'gemstostartclan',
                            'attributes' => [
                                'id' => 'gemstostartclan',
                            ],
                            'options' => [
                                'label' => 'clans.gemstostartclan',
                            ]
                        ]
                    ],
                    // Can clan officers who are also moderators moderate their own clan even if they cannot moderate all clans?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'officermoderate',
                            'attributes' => [
                                'id' => 'officermoderate',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'clans.officermoderate',
                            ]
                        ]
                    ],
                    // Hard sanitize for all but latin chars  in the clan name at creation?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'clannamesanitize',
                            'attributes' => [
                                'id' => 'clannamesanitize',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'clans.clannamesanitize',
                            ]
                        ]
                    ],
                    // Hard sanitizie for all but latin chars in the short name at creation?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'clanshortnamesanitize',
                            'attributes' => [
                                'id' => 'clanshortnamesanitize',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'clans.clanshortnamesanitize',
                            ]
                        ]
                    ],
                    // Length of the short name (max 20)
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'clanshortnamelength',
                            'attributes' => [
                                'id' => 'clanshortnamelength',
                            ],
                            'options' => [
                                'label' => 'clans.clanshortnamelength',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // New Days
            'spec' => [
                'name' => 'newdays',
                'attributes' => [
                    'id' => 'newdays'
                ],
                'options' => [
                    'label' => 'newdays.title'
                ],
                'elements' => [
                    // Game days per calendar day
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'daysperday',
                            'attributes' => [
                                'id' => 'daysperday',
                                'min' => 1,
                                'max' => 24,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'newdays.daysperday'
                            ]
                        ]
                    ],
                    // Extra daily uses in specialty area
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'specialtybonus',
                            'attributes' => [
                                'id' => 'specialtybonus',
                                'min' => 0,
                                'max' => 5,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'newdays.specialtybonus'
                            ]
                        ]
                    ],
                    // Modify (+ or -) the number of turns deducted after a resurrection as an absolute (number) or relative (number followed by %)
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'resurrectionturns',
                            'attributes' => [
                                'id' => 'resurrectionturns'
                            ],
                            'options' => [
                                'label' => 'newdays.resurrectionturns'
                            ]
                        ]
                    ],
                    // What weapon is standard for new players or players who just killed the dragon?
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'startweapon',
                            'attributes' => [
                                'id' => 'startweapon'
                            ],
                            'options' => [
                                'label' => 'newdays.startweapon'
                            ]
                        ]
                    ],
                    // What armor is standard for new players or players who just killed the dragon?
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'startarmor',
                            'attributes' => [
                                'id' => 'startarmor'
                            ],
                            'options' => [
                                'label' => 'newdays.startarmor'
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Forest
            'spec' => [
                'name' => 'forest',
                'attributes' => [
                    'id' => 'forest'
                ],
                'options' => [
                    'label' => 'forest.title'
                ],
                'elements' => [
                    // Forest Fights per day
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'turns',
                            'attributes' => [
                                'id' => 'turns',
                                'min' => 5,
                                'max' => 30,
                                'value' => 5
                            ],
                            'options' => [
                                'label' => 'forest.turns'
                            ]
                        ]
                    ],
                    // Forest Creatures show health
                    [
                        'spec' => [
                            'type' => 'select',
                            'name' => 'forestcreaturebar',
                            'attributes' => [
                                'id' => 'forestcreaturebar'
                            ],
                            'options' => [
                                'label' => 'forest.forestcreaturebar',
                                'value_options' => [
                                    0 => 'forest.forestcreaturebar.text',
                                    1 => 'forest.forestcreaturebar.bar',
                                    2 => 'forest.forestcreaturebar.textbar',
                                ]
                            ]
                        ]
                    ],
                    // Forest Creatures drop at least 1/4 of max gold
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'dropmingold',
                            'attributes' => [
                                'id' => 'dropmingold',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'forest.dropmingold',
                            ]
                        ]
                    ],
                    // Allow players to Seek Suicidally?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'suicide',
                            'attributes' => [
                                'id' => 'suicide',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'forest.suicide',
                            ]
                        ]
                    ],
                    // Minimum DKs before players can Seek Suicidally?
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'suicidedk',
                            'attributes' => [
                                'id' => 'suicidedk',
                            ],
                            'options' => [
                                'label' => 'forest.suicidedk',
                            ]
                        ]
                    ],
                    // In one out of how many fight rounds do enemies do a power attack?
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'forestpowerattackchance',
                            'attributes' => [
                                'id' => 'forestpowerattackchance',
                                'min' => 0,
                                'max' => 100,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'forest.forestpowerattackchance',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Multiplier for the power attack
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'forestpowerattackmulti',
                            'attributes' => [
                                'id' => 'forestpowerattackmulti',
                                'min' => 1,
                                'max' => 10,
                                'value' => 1,
                                'step' => 0.1
                            ],
                            'options' => [
                                'label' => 'forest.forestpowerattackmulti',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Player will find a gem one in X times
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'forestgemchance',
                            'attributes' => [
                                'id' => 'forestgemchance',
                                'min' => 10,
                                'max' => 100,
                                'value' => 10
                            ],
                            'options' => [
                                'label' => 'forest.forestgemchance',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Should monsters which get buffed with extra HP/Att/Def get a gold+exp bonus?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'disablebonuses',
                            'attributes' => [
                                'id' => 'disablebonuses',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'forest.disablebonuses',
                            ]
                        ]
                    ],
                    // What percentage of experience should be lost?
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'forestexploss',
                            'attributes' => [
                                'id' => 'forestexploss',
                                'min' => 10,
                                'max' => 100,
                                'value' => 10
                            ],
                            'options' => [
                                'label' => 'forest.forestexploss',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Multiple Enemies
            'spec' => [
                'name' => 'enemies',
                'attributes' => [
                    'id' => 'enemies'
                ],
                'options' => [
                    'label' => 'enemies.title'
                ],
                'elements' => [
                    // Multiple monsters will attack players above which amount of dragonkills?
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multifightdk',
                            'attributes' => [
                                'id' => 'multifightdk',
                                'min' => 8,
                                'max' => 50,
                                'value' => 8
                            ],
                            'options' => [
                                'label' => 'enemies.multifightdk'
                            ]
                        ]
                    ],
                    // The chance for an attack from multiple enemies is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multichance',
                            'attributes' => [
                                'id' => 'multichance',
                                'min' => 0,
                                'max' => 100,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'enemies.multichance',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Can one creature in the creature table appear in a pack (all monsters you encounter in that fight are duplicates of this?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'allowpackmonsters',
                            'attributes' => [
                                'id' => 'allowpackmonsters',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'enemies.allowpackmonsters',
                            ]
                        ]
                    ],
                    // Need Multiple Enemies to be from a different category (sanity reasons)?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'multicategory',
                            'attributes' => [
                                'id' => 'multicategory',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'enemies.multicategory',
                            ]
                        ]
                    ],
                    // Additional experience (%) per enemy during multifights?
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'addexp',
                            'attributes' => [
                                'id' => 'addexp',
                                'min' => 0,
                                'max' => 15,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'enemies.addexp',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // During multi-fights hand out experience instantly?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'instantexp',
                            'attributes' => [
                                'id' => 'instantexp',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'enemies.instantexp',
                            ]
                        ]
                    ],
                    // The base number of multiple enemies at minimum is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multibasemin',
                            'attributes' => [
                                'id' => 'multibasemin',
                                'min' => 0,
                                'max' => 50,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'enemies.multibasemin',
                            ]
                        ]
                    ],
                    // The base number of multiple enemies at maximum is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multibasemax',
                            'attributes' => [
                                'id' => 'multibasemax',
                                'min' => 0,
                                'max' => 50,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'enemies.multibasemax',
                            ]
                        ]
                    ],
                    // The number of multiple enemies at minimum for slumming is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multislummin',
                            'attributes' => [
                                'id' => 'multislummin',
                                'min' => 0,
                                'max' => 50,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'enemies.multislummin',
                            ]
                        ]
                    ],
                    // The number of multiple enemies at maximum for slumming is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multislummax',
                            'attributes' => [
                                'id' => 'multislummax',
                                'min' => 0,
                                'max' => 50,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'enemies.multislummax',
                            ]
                        ]
                    ],
                    // The number of multiple enemies at minimum for thrill seeking is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multithrillmin',
                            'attributes' => [
                                'id' => 'multithrillmin',
                                'min' => 0,
                                'max' => 50,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'enemies.multithrillmin',
                            ]
                        ]
                    ],
                    // The number of multiple enemies at maximum for thrill seeking is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multithrillmax',
                            'attributes' => [
                                'id' => 'multithrillmax',
                                'min' => 0,
                                'max' => 50,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'enemies.multithrillmax',
                            ]
                        ]
                    ],
                    // The number of multiple enemies at minimum for suicide is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multisuimin',
                            'attributes' => [
                                'id' => 'multisuimin',
                                'min' => 0,
                                'max' => 50,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'enemies.multisuimin',
                            ]
                        ]
                    ],
                    // The number of multiple enemies at maximum for suicide is
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'multisuimax',
                            'attributes' => [
                                'id' => 'multisuimax',
                                'min' => 0,
                                'max' => 50,
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'enemies.multisuimax',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Companions/Mercenaries
            'spec' => [
                'name' => 'companion',
                'attributes' => [
                    'id' => 'companion'
                ],
                'options' => [
                    'label' => 'companion.title'
                ],
                'elements' => [
                    // Enable the usage of companions
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'enablecompanions',
                            'attributes' => [
                                'id' => 'enablecompanions',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'companion.enablecompanions',
                            ]
                        ]
                    ],
                    // How many companions are allowed per player
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'companionsallowed',
                            'attributes' => [
                                'id' => 'companionsallowed',
                            ],
                            'options' => [
                                'label' => 'companion.companionsallowed',
                            ]
                        ]
                    ],
                    // Are companions allowed to level up?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'companionslevelup',
                            'attributes' => [
                                'id' => 'companionslevelup',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'companion.companionslevelup',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Bank Settings
            'spec' => [
                'name' => 'bank',
                'attributes' => [
                    'id' => 'bank'
                ],
                'options' => [
                    'label' => 'bank.title'
                ],
                'elements' => [
                    // Max forest fights remaining to earn interest?
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'fightsforinterest',
                            'attributes' => [
                                'id' => 'fightsforinterest',
                                'min' => 0,
                                'max' => 10,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'bank.fightsforinterest'
                            ]
                        ]
                    ],
                    // Max Interest Rate (%)
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'maxinterest',
                            'attributes' => [
                                'id' => 'maxinterest',
                                'min' => 5,
                                'max' => 10,
                                'value' => 5
                            ],
                            'options' => [
                                'label' => 'bank.maxinterest'
                            ]
                        ]
                    ],
                    // Min Interest Rate (%)
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'mininterest',
                            'attributes' => [
                                'id' => 'mininterest',
                                'min' => 0,
                                'max' => 5,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'bank.mininterest'
                            ]
                        ]
                    ],
                    // Over what amount of gold does the bank cease paying interest?
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'maxgoldforinterest',
                            'attributes' => [
                                'id' => 'maxgoldforinterest',
                                'value' => 100000
                            ],
                            'options' => [
                                'label' => 'bank.maxgoldforinterest'
                            ]
                        ]
                    ],
                    // Max player can borrow per level (val * level for max)
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'borrowperlevel',
                            'attributes' => [
                                'id' => 'borrowperlevel',
                                'min' => 5,
                                'max' => 200,
                                'value' => 5
                            ],
                            'options' => [
                                'label' => 'bank.borrowperlevel',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Allow players to transfer gold
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'allowgoldtransfer',
                            'attributes' => [
                                'id' => 'allowgoldtransfer',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'bank.allowgoldtransfer'
                            ]
                        ]
                    ],
                    // Max player can receive from a transfer (val * level)
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'transferperlevel',
                            'attributes' => [
                                'id' => 'transferperlevel',
                                'min' => 5,
                                'max' => 100,
                                'value' => 5
                            ],
                            'options' => [
                                'label' => 'bank.transferperlevel',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Min level a player (0 DK's) needs to transfer gold
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'mintransferlev',
                            'attributes' => [
                                'id' => 'mintransferlev',
                                'min' => 1,
                                'max' => 5,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'bank.mintransferlev',
                                // 'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Total transfers a player can receive in one day
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'transferreceive',
                            'attributes' => [
                                'id' => 'transferreceive',
                                'min' => 0,
                                'max' => 5,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'bank.transferreceive',
                                // 'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Amount player can transfer to others (val * level)
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'maxtransferout',
                            'attributes' => [
                                'id' => 'maxtransferout',
                                'min' => 5,
                                'max' => 100,
                                'value' => 5
                            ],
                            'options' => [
                                'label' => 'bank.maxtransferout',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Fee for express inn payment (x or x%)
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'innfee',
                            'attributes' => [
                                'id' => 'innfee',
                                'value' => 0
                            ],
                            'options' => [
                                'label' => 'bank.innfee'
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Mail Settings
            'spec' => [
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
            ]
        ],
        [
            // PvP
            'spec' => [
                'name' => 'pvp',
                'attributes' => [
                    'id' => 'pvp'
                ],
                'options' => [
                    'label' => 'pvp.title'
                ],
                'elements' => [
                    // Enable Slay Other Players
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'pvp',
                            'attributes' => [
                                'id' => 'pvp',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'pvp.pvp',
                            ]
                        ]
                    ],
                    // Timeout in seconds to wait after a player was PvP'd
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'pvptimeout',
                            'attributes' => [
                                'id' => 'pvptimeout',
                            ],
                            'options' => [
                                'label' => 'pvp.pvptimeout',
                            ]
                        ]
                    ],
                    // Player Fights per day
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'pvpday',
                            'attributes' => [
                                'id' => 'pvpday',
                                'min' => 1,
                                'max' => 10
                            ],
                            'options' => [
                                'label' => 'pvp.pvpday',
                            ]
                        ]
                    ],
                    // Can players be engaged in pvp after a DK until they visit the village again?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'pvpdragonoptout',
                            'attributes' => [
                                'id' => 'pvpdragonoptout',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'pvp.pvpdragonoptout',
                            ]
                        ]
                    ],
                    // How many levels can attacker & defender be different? (-1=any - lower limit is always +1)
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'pvprange',
                            'attributes' => [
                                'id' => 'pvprange',
                                'min' => -1,
                                'max' => 15,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'pvp.pvprange',
                            ]
                        ]
                    ],
                    // Days that new players are safe from PvP
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'pvpimmunity',
                            'attributes' => [
                                'id' => 'pvpimmunity',
                                'min' => 1,
                                'max' => 5,
                                'value' => 1
                            ],
                            'options' => [
                                'label' => 'pvp.pvpimmunity',
                            ]
                        ]
                    ],
                    // Experience below which player is safe from PvP
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'pvpminexp',
                            'attributes' => [
                                'id' => 'pvpminexp',
                                'value' => 1500
                            ],
                            'options' => [
                                'label' => 'pvp.pvpminexp',
                            ]
                        ]
                    ],
                    // Percent of victim experience attacker gains on win
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'pvpattgain',
                            'attributes' => [
                                'id' => 'pvpattgain',
                                'min' => 0.25,
                                'max' => 20,
                                'step' => 0.25,
                                'value' => 0.25
                            ],
                            'options' => [
                                'label' => 'pvp.pvpattgain',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Percent of experience attacker loses on loss
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'pvpattlose',
                            'attributes' => [
                                'id' => 'pvpattlose',
                                'min' => 0.25,
                                'max' => 20,
                                'step' => 0.25,
                                'value' => 0.25
                            ],
                            'options' => [
                                'label' => 'pvp.pvpattlose',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Percent of attacker experience defender gains on win
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'pvpdefgain',
                            'attributes' => [
                                'id' => 'pvpdefgain',
                                'min' => 0.25,
                                'max' => 20,
                                'step' => 0.25,
                                'value' => 0.25
                            ],
                            'options' => [
                                'label' => 'pvp.pvpdefgain',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Percent of experience defender loses on loss
                    [
                        'spec' => [
                            'type' => 'range',
                            'name' => 'pvpdeflose',
                            'attributes' => [
                                'id' => 'pvpdeflose',
                                'min' => 0.25,
                                'max' => 20,
                                'step' => 0.25,
                                'value' => 0.25,
                            ],
                            'options' => [
                                'label' => 'pvp.pvpdeflose',
                                'disable_slider_labels' => true,
                            ]
                        ]
                    ],
                    // Is the maximum amount a successful attacker or defender can gain limited?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'pvphardlimit',
                            'attributes' => [
                                'id' => 'pvphardlimit',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'pvp.pvphardlimit',
                            ]
                        ]
                    ],
                    // If yes - What is the maximum amount of EXP he can get?
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'pvphardlimitamount',
                            'attributes' => [
                                'id' => 'pvphardlimitamount',
                            ],
                            'options' => [
                                'label' => 'pvp.pvphardlimitamount',
                            ]
                        ]
                    ],
                    // Can players attack others with same ID?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'pvpsameid',
                            'attributes' => [
                                'id' => 'pvpsameid',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'pvp.pvpsameid',
                            ]
                        ]
                    ],
                    // Can players attack others with same IP?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'pvpsameip',
                            'attributes' => [
                                'id' => 'pvpsameip',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'pvp.pvpsameip',
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Content Expiration
            'spec' => [
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
                            ],
                            'options' => [
                                'label' => 'content.expirenotificationdays',
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
            ]
        ],
        [
            // LoGDnet Setup
            'spec' => [
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
            ]
        ],
        [
            // Game day Setup
            'spec' => [
                'name' => 'daysetup',
                'attributes' => [
                    'id' => 'daysetup'
                ],
                'options' => [
                    'label' => 'daysetup.title'
                ],
                'elements' => [
                    // Show the village game time in what format?
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'gametime',
                            'attributes' => [
                                'id' => 'gametime',
                            ],
                            'options' => [
                                'label' => 'daysetup.gametime.label',
                                'note' => 'daysetup.gametime.note'
                            ]
                        ]
                    ],
                    // Day Duration
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'dayduration',
                            'attributes' => [
                                'id' => 'dayduration',
                                'disabled' => true,
                            ],
                            'options' => [
                                'label' => 'daysetup.dayduration'
                            ]
                        ]
                    ],
                    // Current game time
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'curgametime',
                            'attributes' => [
                                'id' => 'curgametime',
                                'disabled' => true,
                            ],
                            'options' => [
                                'label' => 'daysetup.curgametime'
                            ]
                        ]
                    ],
                    // Current Server Time
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'curservertime',
                            'attributes' => [
                                'id' => 'curservertime',
                                'disabled' => true,
                            ],
                            'options' => [
                                'label' => 'daysetup.curservertime'
                            ]
                        ]
                    ],
                    // Last new day
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'lastnewday',
                            'attributes' => [
                                'id' => 'lastnewday',
                                'disabled' => true,
                            ],
                            'options' => [
                                'label' => 'daysetup.lastnewday'
                            ]
                        ]
                    ],
                    // Next new day
                    [
                        'spec' => [
                            'type' => 'text',
                            'name' => 'nextnewday',
                            'attributes' => [
                                'id' => 'nextnewday',
                                'disabled' => true,
                            ],
                            'options' => [
                                'label' => 'daysetup.nextnewday'
                            ]
                        ]
                    ],
                    // Real time to offset new day
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'gameoffsetseconds',
                            'attributes' => [
                                'id' => 'gameoffsetseconds',
                            ],
                            'options' => [
                                'label' => 'daysetup.gameoffsetseconds'
                            ]
                        ]
                    ],
                ]
            ]
        ],
        [
            // Miscellaneous Settings
            'spec' => [
                'name' => 'misc',
                'attributes' => [
                    'id' => 'misc'
                ],
                'options' => [
                    'label' => 'misc.title'
                ],
                'elements' => [
                    // Cost to resurrect from the dead?
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'resurrectioncost',
                            'attributes' => [
                                'id' => 'resurrectioncost',
                                'required' => false,
                                'value' => 100
                            ],
                            'options' => [
                                'label' => 'misc.resurrectioncost'
                            ]
                        ]
                    ],
                    // The Barkeeper may help you to switch your specialty?
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'allowspecialswitch',
                            'attributes' => [
                                'id' => 'allowspecialswitch',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'misc.allowspecialswitch',
                            ]
                        ]
                    ],
                    // Maximum number of items to be shown in the warrior list
                    [
                        'spec' => [
                            'type' => 'number',
                            'name' => 'maxlistsize',
                            'attributes' => [
                                'id' => 'maxlistsize',
                                'required' => false,
                                'value' => 25
                            ],
                            'options' => [
                                'label' => 'misc.maxlistsize'
                            ]
                        ]
                    ],
                    // Does Merick have feed onhand for creatures
                    [
                        'spec' => [
                            'type' => 'checkbox',
                            'name' => 'allowfeed',
                            'attributes' => [
                                'id' => 'allowfeed',
                                'class' => 'lotgd toggle'
                            ],
                            'options' => [
                                'label' => 'misc.allowfeed',
                            ]
                        ]
                    ],
                ]
            ]
        ],
    ],
    'input_filter' => 'Lotgd\Core\Form\Filter\Configuration'
];
