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

use Laminas\Form\ElementInterface;
use Twig\Environment as Environment;
use Twig\TwigFunction;

class FormRange extends FormInput
{
    /**
     * Attributes valid for the input tag type="range".
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name'         => true,
        'autocomplete' => true,
        'autofocus'    => true,
        'disabled'     => true,
        'form'         => true,
        'list'         => true,
        'max'          => true,
        'min'          => true,
        'step'         => true,
        'required'     => true,
        'type'         => true,
        'value'        => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('laminas_form_range', [$this, 'render'], ['needs_environment' => true]),
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
            throw new Exception\DomainException(\sprintf('%s requires that the element has an assigned name; none discovered', __METHOD__));
        }

        $attributes          = $element->getAttributes();
        $attributes['name']  = $name;
        $type                = $this->getType($element);
        $attributes['type']  = $type;
        $attributes['value'] = $element->getValue();

        return $env->render('{theme}/form/element/range.html.twig', [
            'element'             => $element,
            'attributesString'    => $this->createAttributesString($env, $attributes),
            'disableSliderLabels' => $element->getOption('disable_slider_labels'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-range';
    }

    /**
     * Determine input type to use.
     *
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'hidden';
    }
}
