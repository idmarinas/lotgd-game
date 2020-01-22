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
use Zend\Form\ElementInterface;

class FormHidden extends FormInput
{
    /**
     * Attributes valid for the input tag type="hidden".
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name' => true,
        'disabled' => true,
        'form' => true,
        'type' => true,
        'value' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_hidden', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-hidden';
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(ElementInterface $element)
    {
        return 'hidden';
    }
}
