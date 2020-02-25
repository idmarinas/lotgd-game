<?php

return [
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
                    'class' => 'lotgd toggle',
                    'value' => 1
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
                    'disable_slider_labels' => true,
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
                    'disable_slider_labels' => true,
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
];
