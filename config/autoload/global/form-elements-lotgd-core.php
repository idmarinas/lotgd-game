<?php

use Zend\Form\Element as ZendElement;
use Lotgd\Core\Form\Element;
use Zend\Form\ElementFactory;
use Lotgd\Core\Form\ElementFactory as LotgdElementFactory;

return [
    'form_elements' => [
        'aliases' => [
            //-- Select language for game
            'gamelanguage' => Element\GameLanguage::class,
            'gameLanguage' => Element\GameLanguage::class,
            'GameLanguage' => Element\GameLanguage::class,

            //-- Select languages for server
            'serverlanguage' => Element\ServerLanguage::class,
            'serverLanguage' => Element\ServerLanguage::class,
            'ServerLanguage' => Element\ServerLanguage::class,

            //-- Tagify element
            'tagify' => Element\Tagify::class,
            'Tagify' => Element\Tagify::class,
            'tags' => Element\Tagify::class,
            'Tags' => Element\Tagify::class,

            //-- Select of themes.
            'lotgdtheme' => Element\LotgdTheme::class,
            'lotgdTheme' => Element\LotgdTheme::class,
            'LotgdTheme' => Element\LotgdTheme::class,

            //-- Select BitField.
            'bitfield' => Element\BitField::class,
            'bitField' => Element\BitField::class,
            'BitField' => Element\BitField::class,
        ],
        'factories' => [
            Element\GameLanguage::class => LotgdElementFactory::class,
            Element\ServerLanguage::class => ElementFactory::class,
            Element\Tagify::class => ElementFactory::class,
            Element\LotgdTheme::class => LotgdElementFactory::class,
            Element\BitField::class => ElementFactory::class,
        ]
    ]
];
