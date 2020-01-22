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
use Zend\Form\LabelAwareInterface;

class FormButton extends FormInput
{
    /**
     * Attributes valid for the button tag.
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name' => true,
        'autofocus' => true,
        'disabled' => true,
        'form' => true,
        'formaction' => true,
        'formenctype' => true,
        'formmethod' => true,
        'formnovalidate' => true,
        'formtarget' => true,
        'type' => true,
        'value' => true,
    ];

    /**
     * Valid values for the button type.
     *
     * @var array
     */
    protected $validTypes = [
        'button' => 'button',
        'reset' => 'reset',
        'submit' => 'submit',
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_button', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render a form <button> element from the provided $element,
     * using content from $buttonContent or the element's "label" attribute.
     *
     * @param string|null $buttonContent
     *
     * @throws Exception\DomainException
     *
     * @return string
     */
    public function render(Environment $env, ElementInterface $element, $buttonContent = null)
    {
        $openTag = $this->openTag($env, $element);

        $label = $element->getLabel();
        $translator = $this->getTranslator();

        if (null === $label)
        {
            throw new Exception\DomainException(sprintf('%s expects either button content as the second argument, '.'or that the element provided has a label value; neither found', __METHOD__));
        }

        $label = $translator->translate($label, $element->getOption('translator_text_domain') ?: $this->getTranslatorTextDomain());

        if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape'))
        {
            $escapeHtmlHelper = $env->getFilter('escape')->getCallable();
            $label = $escapeHtmlHelper($env, $label, 'html');
        }

        return $openTag.$label.$this->closeTag();
    }

    /**
     * Generate an opening button tag.
     *
     * @param array|ElementInterface|null $attributesOrElement
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     *
     * @return string
     */
    public function openTag(Environment $env, $attributesOrElement = null)
    {
        if (null === $attributesOrElement)
        {
            return '<button>';
        }

        if (is_array($attributesOrElement))
        {
            $attributes = $this->createAttributesString($env, $attributesOrElement);

            return sprintf('<button %s>', $attributes);
        }

        if (! $attributesOrElement instanceof ElementInterface)
        {
            throw new Exception\InvalidArgumentException(sprintf('%s expects an array or Zend\Form\ElementInterface instance; received "%s"', __METHOD__, (is_object($attributesOrElement) ? get_class($attributesOrElement) : gettype($attributesOrElement))));
        }

        $element = $attributesOrElement;
        $name = $element->getName();

        if (empty($name) && 0 !== $name)
        {
            throw new Exception\DomainException(sprintf('%s requires that the element has an assigned name; none discovered', __METHOD__));
        }

        $attributes = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getType($element);
        $attributes['value'] = $element->getValue();

        return sprintf(
            '<button %s>',
            $this->createAttributesString($env, $attributes)
        );
    }

    /**
     * Return a closing button tag.
     *
     * @return string
     */
    public function closeTag()
    {
        return '</button>';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-button';
    }

    /**
     * Determine button type to use.
     *
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        $type = (string) $element->getAttribute('type');
        $type = strtolower($type);

        return $this->validTypes[$type] ?? 'submit';
    }
}
