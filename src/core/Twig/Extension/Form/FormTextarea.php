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
use Laminas\Form\Exception;
use Twig\Environment as Environment;
use Twig\TwigFunction;

class FormTextarea extends AbstractElement
{
    /**
     * Attributes valid for the input tag.
     *
     * @var array
     */
    protected $validTagAttributes = [
        'autocomplete' => true,
        'autofocus'    => true,
        'cols'         => true,
        'dirname'      => true,
        'disabled'     => true,
        'form'         => true,
        'maxlength'    => true,
        'name'         => true,
        'placeholder'  => true,
        'readonly'     => true,
        'required'     => true,
        'rows'         => true,
        'wrap'         => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('laminas_form_textarea', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render a form <textarea> element from the provided $element.
     *
     * @throws Exception\DomainException
     *
     * @return string
     */
    public function render(Environment $env, ElementInterface $element)
    {
        $name = $element->getName();

        if (empty($name) && 0 !== $name)
        {
            throw new Exception\DomainException(\sprintf('%s requires that the element has an assigned name; none discovered', __METHOD__));
        }

        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $content            = (string) $element->getValue();
        $escapeHtml         = $env->getFilter('escape')->getCallable();

        return \sprintf(
            '<textarea %s>%s</textarea>',
            $this->createAttributesString($env, $attributes),
            $escapeHtml($env, $content, 'html')
        );
    }

    /**
     * Determine input type to use.
     *
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'textarea';
    }
}
