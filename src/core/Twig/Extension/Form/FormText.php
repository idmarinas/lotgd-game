<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Twig\Extension\Form;

use Twig\TwigFunction;
use Laminas\Form\ElementInterface;

class FormText extends FormInput
{
    /**
     * Attributes valid for the input tag type="text".
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name' => true,
        'autocomplete' => true,
        'autofocus' => true,
        'dirname' => true,
        'disabled' => true,
        'form' => true,
        'list' => true,
        'maxlength' => true,
        'minlength' => true,
        'pattern' => true,
        'placeholder' => true,
        'readonly' => true,
        'required' => true,
        'size' => true,
        'type' => true,
        'value' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('laminas_form_text', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-text';
    }

    /**
     * Determine input type to use.
     *
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'text';
    }
}
