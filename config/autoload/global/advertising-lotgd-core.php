<?php

/**
 * Configuration base for Advertising in LoTGD.
 */

return [
    'advertising' => [
        //-- By default use AdSense in LoTGD
        //-- layout.html.twig add url for AdSense auto, if google AdSense are activated
        'google' => [ //-- This configuration is for Google Adsense
            'client' => null, //-- "data-ad-client" ca-pub-XXXXXXX11XXX9
            //-- Ads blocks one for each banner you need
            'ad_header' => [
                'style' => 'display:block', //-- style="" tag in <ins>
                'slot' => null, //-- "data-ad-slot" Slot ID of Ad block 8XXXXX1
                'format' => 'auto', // "data-ad-format" Values: "rectangle", "vertical" or "horizontal"
                'responsive' => 'true', // "data-full-width-responsive"
            ],
            'ad_column_navigation_bottom' => [
                'style' => 'display:block',
                'slot' => null,
                'format' => 'auto',
                'responsive' => 'true',
            ]
            //-- Google AdSense are activated if client are configured (not check if is valid client)
        ],
        //-- Can add other system with name key
        //-- Need add a handler for this ad system
        //-- Can use same pattern for Google ads.
    ]
];
