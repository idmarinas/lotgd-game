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

use Lotgd\Core\Template\Theme as Environment;
use Twig\TwigFunction;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class FormInput extends AbstractElement
{
    /**
     * Attributes valid for the input tag.
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name' => true,
        'accept' => true,
        'alt' => true,
        'autocomplete' => true,
        'autofocus' => true,
        'checked' => true,
        'dirname' => true,
        'disabled' => true,
        'form' => true,
        'formaction' => true,
        'formenctype' => true,
        'formmethod' => true,
        'formnovalidate' => true,
        'formtarget' => true,
        'height' => true,
        'list' => true,
        'max' => true,
        'maxlength' => true,
        'min' => true,
        'multiple' => true,
        'pattern' => true,
        'placeholder' => true,
        'readonly' => true,
        'required' => true,
        'size' => true,
        'src' => true,
        'step' => true,
        'type' => true,
        'value' => true,
        'width' => true,
    ];

    /**
     * Valid values for the input type.
     *
     * @var array
     */
    protected $validTypes = [
        'text' => 'text',
        'button' => 'button',
        'checkbox' => 'checkbox',
        'file' => 'file',
        'hidden' => 'hidden',
        'image' => 'image',
        'password' => 'password',
        'radio' => 'radio',
        'reset' => 'reset',
        'select' => 'select',
        'submit' => 'submit',
        'color' => 'color',
        'date' => 'date',
        'datetime' => 'datetime',
        'datetime-local' => 'datetime-local',
        'email' => 'email',
        'month' => 'month',
        'number' => 'number',
        'range' => 'range',
        'search' => 'search',
        'tel' => 'tel',
        'time' => 'time',
        'url' => 'url',
        'week' => 'week',
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_input', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render a form <input> element from the provided $element.
     *
     * @throws Exception\DomainException
     *
     * @return string
     */
    public function render(Environment $env, ElementInterface $element)
    {
        $name = $element->getName();

        if (null === $name || '' === $name)
        {
            throw new Exception\DomainException(sprintf('%s requires that the element has an assigned name; none discovered', __METHOD__));
        }

        $attributes = $element->getAttributes();
        $attributes['name'] = $name;
        $type = $this->getType($element);
        $attributes['type'] = $type;
        $attributes['value'] = $element->getValue();

        //-- Avoid populate password input
        if ('password' == $type)
        {
            $attributes['value'] = '';
        }

        return sprintf('<input %s>',
            $this->createAttributesString($env, $attributes)
        );
    }

    /**
     * Determine input type to use.
     *
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        $type = (string) $element->getAttribute('type');

        $type = strtolower($type);

        return $this->validTypes[$type] ?? 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-input';
    }
}
