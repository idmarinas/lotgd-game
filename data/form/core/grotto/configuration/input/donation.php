<?php

return [
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
];
