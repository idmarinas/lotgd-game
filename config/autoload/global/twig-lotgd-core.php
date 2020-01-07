<?php

/**
 * All this Extension are required for Game Core
 *
 * Extension\Class\Name::class
 */

return [
    'twig_extensions' => [//-- Custom extensions for Twig
        Lotgd\Core\Twig\Extension\GameCore::class,
        Lotgd\Core\Twig\Extension\FlashMessages::class,
        Lotgd\Core\Twig\Extension\Motd::class,
        Lotgd\Core\Twig\Extension\Navigation::class,
        Lotgd\Core\Twig\Extension\Translator::class,
        Lotgd\Core\Twig\Extension\Commentary::class,

        //-- Added in version 4.1.0
        Lotgd\Core\Twig\Extension\Form::class,

        //-- Extension of a third party
        Marek\Twig\ByteUnitsExtension::class,
    ]
];
