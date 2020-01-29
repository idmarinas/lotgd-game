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
        Lotgd\Core\Twig\Extension\Form\Form::class,
        Lotgd\Core\Twig\Extension\Form\FormAction::class,
        Lotgd\Core\Twig\Extension\Form\FormBitField::class,
        Lotgd\Core\Twig\Extension\Form\FormButton::class,
        Lotgd\Core\Twig\Extension\Form\FormCheckbox::class,
        Lotgd\Core\Twig\Extension\Form\FormCollection::class,
        Lotgd\Core\Twig\Extension\Form\FormElement::class,
        Lotgd\Core\Twig\Extension\Form\FormElementError::class,
        Lotgd\Core\Twig\Extension\Form\FormEmail::class,
        Lotgd\Core\Twig\Extension\Form\FormHidden::class,
        Lotgd\Core\Twig\Extension\Form\FormInput::class,
        Lotgd\Core\Twig\Extension\Form\FormLabel::class,
        Lotgd\Core\Twig\Extension\Form\FormNote::class,
        Lotgd\Core\Twig\Extension\Form\FormNumber::class,
        Lotgd\Core\Twig\Extension\Form\FormRange::class,
        Lotgd\Core\Twig\Extension\Form\FormRow::class,
        Lotgd\Core\Twig\Extension\Form\FormSelect::class,
        Lotgd\Core\Twig\Extension\Form\FormTagify::class,
        Lotgd\Core\Twig\Extension\Form\FormText::class,
        Lotgd\Core\Twig\Extension\Form\FormTextarea::class,

        //-- Extension of a third party
        Marek\Twig\ByteUnitsExtension::class,
    ]
];
