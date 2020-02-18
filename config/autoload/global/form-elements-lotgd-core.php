<?php

use DoctrineModule\Form\Element as DoctrineElement;
use DoctrineORMModule\Service as DoctrineService;
use Lotgd\Core\Form\Element;
use Lotgd\Core\Form\ElementFactory as LotgdElementFactory;
use Zend\Form\ElementFactory;

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

            //-- Added in version 4.2.0
            //-- Doctrine elements
            'objectselect' => DoctrineElement\ObjectSelect::class,
            'objectSelect' => DoctrineElement\ObjectSelect::class,
            'ObjectSelect' => DoctrineElement\ObjectSelect::class,
            'objectradio' => DoctrineElement\ObjectRadio::class,
            'objectRadio' => DoctrineElement\ObjectRadio::class,
            'ObjectRadio' => DoctrineElement\ObjectRadio::class,
            'objectmulticheckbox' => DoctrineElement\ObjectMultiCheckbox::class,
            'objectMulticheckbox' => DoctrineElement\ObjectMultiCheckbox::class,
            'objectMultiCheckbox' => DoctrineElement\ObjectMultiCheckbox::class,
            'ObjectMultiCheckbox' => DoctrineElement\ObjectMultiCheckbox::class,
        ],
        'factories' => [
            Element\GameLanguage::class => LotgdElementFactory::class,
            Element\ServerLanguage::class => ElementFactory::class,
            Element\Tagify::class => ElementFactory::class,
            Element\LotgdTheme::class => LotgdElementFactory::class,
            Element\BitField::class => ElementFactory::class,

            //-- Added in version 4.2.0
            DoctrineElement\ObjectSelect::class => DoctrineService\ObjectSelectFactory::class,
            DoctrineElement\ObjectRadio::class => DoctrineService\ObjectRadioFactory::class,
            DoctrineElement\ObjectMultiCheckbox::class => DoctrineService\ObjectMultiCheckboxFactory::class,
        ]
    ]
];
