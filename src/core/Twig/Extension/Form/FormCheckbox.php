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
use Zend\Form\Element\Checkbox as CheckboxElement;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class FormCheckbox extends FormInput
{
    /**
     * Render a form <input> element from the provided $element.
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     *
     * @return string
     */
    public function render(Environment $env, ElementInterface $element)
    {
        if (! $element instanceof CheckboxElement)
        {
            throw new Exception\InvalidArgumentException(sprintf('%s requires that the element is of type Zend\Form\Element\Checkbox', __METHOD__));
        }

        $name = $element->getName();

        if (empty($name) && 0 !== $name)
        {
            throw new Exception\DomainException(sprintf('%s requires that the element has an assigned name; none discovered', __METHOD__));
        }

        $labelHelper = $env->getExtension(FormLabel::class);
        $labelHelper->setTranslatorTextDomain($element->getOptions()['translator_text_domain'] ?? $this->getTranslatorTextDomain());

        $attributes = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getInputType();
        $attributes['value'] = $element->getCheckedValue();

        if ($element->isChecked())
        {
            $attributes['checked'] = 'checked';
        }

        $rendered = sprintf(
            '<input %s>',
            $this->createAttributesString($env, $attributes)
        );

        if ($element->useHiddenElement())
        {
            $hiddenAttributes = [
                'disabled' => $attributes['disabled'] ?? false,
                'name' => $attributes['name'],
                'value' => $element->getUncheckedValue(),
            ];

            $rendered = sprintf(
                '<input type="hidden" %s>',
                $this->createAttributesString($env, $hiddenAttributes)
            ).$rendered;
        }

        return sprintf('<div class="ui checkbox %s">%s %s</div>',
            $attributes['class'] ?? '',
            $rendered,
            $labelHelper->render($env, $element)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_checkbox', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Return input type.
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'checkbox';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-checkbox';
    }
}
