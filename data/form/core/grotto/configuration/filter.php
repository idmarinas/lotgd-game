<?php

use Zend\Filter;
use Zend\Validator;

use function PHPSTORM_META\map;

return [
    'game_setup' => [
        'type' => \Zend\InputFilter\InputFilter::class, //-- Need this when is a Field set
        [
            'name' => 'allowcreation',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'servername',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'serverurl',
            'required' => false,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                [
                    'name' => Filter\UriNormalize::class,
                    'options' => [
                        'enforcedScheme' => 'https'
                    ]
                ]
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Uri::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'loginbanner',
            'required' => false,
            'filters' => [
                [
                    'name' => Filter\StringTrim::class
                ],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'maxonline',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'gameadminemail',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\EmailAddress::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'emailpetitions',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'petition_types',
            'required' => true,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'edittitles',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'motditems',
            'required' => true,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ]
    ],
    'maintenance' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'debug',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maintenance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'fullmaintenance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maintenancenote',
            'required' => false,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class]
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'maintenanceauthor',
            'required' => false,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class]
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
    ],
    'home' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'homeskinselect',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'homecurtime',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'homenewdaytime',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'homenewestplayer',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'defaultskin',
            'required' => true,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
            ]
        ],
        [
            'name' => 'impressum',
            'required' => false,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class]
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
    ],
    'beta' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'beta',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'betaperplayer',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'account' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'defaultsuperuser',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
            ]
        ],
        [
            'name' => 'newplayerstartgold',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maxrestartgold',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maxrestartgems',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'playerchangeemail',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'playerchangeemailauto',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'playerchangeemaildays',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'validationtarget',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'requireemail',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'requirevalidemail',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'blockdupeemail',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'spaceinname',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'allowoddadminrenames',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'selfdelete',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'commentary' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'soap',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
            ]
        ],
        [
            'name' => 'maxcolors',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class],
            ]
        ],
        [
            'name' => 'postinglimit',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
            ]
        ],
        [
            'name' => 'chatlinelength',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class],
            ]
        ],
        [
            'name' => 'maxchars',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class],
            ]
        ],
        [
            'name' => 'moderateexcludes',
            'required' => false,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class]
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ]
                ],
            ]
        ],
    ],
    'places' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'villagename',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'innname',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'barkeep',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'barmaid',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'bard',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'clanregistrar',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'deathoverlord',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'bankername',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
    ],
    'su_title' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'enable_chat_tags',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
            ]
        ],
        [
            'name' => 'chat_tag_megauser',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'chat_tag_gm',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
        [
            'name' => 'chat_tag_mod',
            'required' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
            ],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ],
        ],
    ],
    'referral' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'refereraward',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'referminlevel',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'events' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'forestchance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'villagechance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'innchance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'gravechance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'gardenchance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'donation' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'dpointspercurrencyunit',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'paypalemail',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\EmailAddress::class]
            ]
        ],
        [
            'name' => 'paypalcurrency',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 100
                    ],
                ],
            ]
        ],
        [
            'name' => 'paypalcountry-code',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class]
            ]
        ],
        [
            'name' => 'paypaltext',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ]
        ],
    ],
    'combat' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'autofight',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'autofightfull',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class]
            ]
        ],
    ],

    'training' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'automaster',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multimaster',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'displaymasternews',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maxlevel',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'exp-array',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class]
            ]
        ],
    ],

    'clans' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'allowclans',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'goldtostartclan',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'gemstostartclan',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'officermoderate',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'clannamesanitize',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'clanshortnamesanitize',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'clanshortnamelength',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class],
                [
                    'name' => Validator\Between::class,
                    'options' => [
                        'min' => 3,
                        'max' => 20
                    ]
                ]
            ]
        ],
    ],
    'newdays' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'daysperday',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'specialtybonus',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'resurrectionturns',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'startweapon',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'startarmor',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
    ],
    'forest' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'turns',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'forestcreaturebar',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'dropmingold',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'suicide',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'suicidedk',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'forestpowerattackchance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'forestpowerattackmulti',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => \Zend\I18n\Validator\IsFloat::class]
            ]
        ],
        [
            'name' => 'forestgemchance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'disablebonuses',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'forestexploss',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'enemies' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'multifightdk',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multichance',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'allowpackmonsters',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multicategory',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'addexp',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'instantexp',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maxattacks',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multibasemin',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multibasemax',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multislummin',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multislummax',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multithrillmin',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multithrillmax',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multisuimin',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'multisuimax',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'companion' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'enablecompanions',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'companionsallowed',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'companionslevelup',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'bank' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'fightsforinterest',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maxinterest',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'mininterest',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maxgoldforinterest',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'borrowperlevel',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'allowgoldtransfer',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'transferperlevel',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'mintransferlev',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'transferreceive',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maxtransferout',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'innfee',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'mail' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'mailsizelimit',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'inboxlimit',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'oldmail',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'superuseryommessage',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'onlyunreadmails',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'pvp' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'pvp',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvptimeout',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvpday',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvpdragonoptout',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvprange',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvpimmunity',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvpminexp',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Zend\I18n\Validator\IsFloat::class]
            ]
        ],
        [
            'name' => 'pvpattgain',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Zend\I18n\Validator\IsFloat::class]
            ]
        ],
        [
            'name' => 'pvpattlose',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Zend\I18n\Validator\IsFloat::class]
            ]
        ],
        [
            'name' => 'pvpdefgain',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Zend\I18n\Validator\IsFloat::class]
            ]
        ],
        [
            'name' => 'pvpdeflose',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Zend\I18n\Validator\IsFloat::class]
            ]
        ],
        [
            'name' => 'pvphardlimit',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvphardlimitamount',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvpsameid',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'pvpsameip',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'content' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'expirecontent',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'expiredebuglog',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'expirefaillog',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'expiregamelog',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'expiretrashacct',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'expirenewacct',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'expirenotificationdays',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'expireoldacct',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'LOGINTIMEOUT',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'logdnet' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'logdnet',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'serverdesc',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 75
                    ],
                ],
            ]
        ],
        [
            'name' => 'logdnetserver',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 255
                    ],
                ],
            ]
        ],
        [
            'name' => 'curltimeout',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
    'daysetup' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'gametime',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 0,
                        'max' => 100
                    ],
                ],
            ]
        ],
        [
            'name' => 'gameoffsetseconds',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Zend\I18n\Validator\IsInt::class]
            ]
        ],
    ],
    'misc' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        [
            'name' => 'resurrectioncost',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'allowspecialswitch',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'maxlistsize',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
        [
            'name' => 'allowfeed',
            'required' => false,
            'filters' => [],
            'validators' => [
                ['name' => Validator\NotEmpty::class],
                ['name' => Validator\Digits::class]
            ]
        ],
    ],
];
