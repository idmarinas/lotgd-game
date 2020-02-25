<?php

return [
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
                    'id' => 'newplayerstartgold',
                    'value' => 50
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
                    'id' => 'maxrestartgold',
                    'value' => 50
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
                    'id' => 'maxrestartgems',
                    'value' => 10
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
                        0 => 'account.validationtarget.options.old',
                        1 => 'account.validationtarget.options.new',
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
];
