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

class FormLabel extends AbstractElement
{
    const APPEND = 'append';
    const PREPEND = 'prepend';

    /**
     * Attributes valid for the label tag.
     *
     * @var array
     */
    protected $validTagAttributes = [
        'for' => true,
        'form' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_label', [$this, 'render'], ['needs_environment' => true]),
            new TwigFunction('form_label_open_tag', [$this, 'openTag'], ['needs_environment' => true]),
            new TwigFunction('form_label_close_tag', [$this, 'closeTag'], ['needs_environment' => true]),
        ];
    }

    /**
     * Generate a form label.
     *
     * @param ElementInterface $element
     *
     * @throws Exception\DomainException
     *
     * @return string|FormLabel
     */
    public function render(Environment $env, ElementInterface $element = null, ?string $translatorTextDomain = null)
    {
        if (! $element || empty($element->getLabel()))
        {
            throw new Exception\DomainException(sprintf('%s expects either label content as the second argument, or that the element provided has a label attribute; neither found', __METHOD__));
        }

        return $env->renderThemeTemplate('form/element/label.twig', [
            'element' => $element,
            'escapeLabel' => (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')),
            'translatorTextDomain' => $element->getOptions()['translator_text_domain'] ?? $translatorTextDomain ?: $this->getTranslatorTextDomain()
        ]);
    }

    /**
     * Generate an opening label tag.
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
            return '<label>';
        }

        if (is_array($attributesOrElement))
        {
            $attributes = $this->createAttributesString($env, $attributesOrElement);

            return sprintf('<label %s>', $attributes);
        }

        if (! $attributesOrElement instanceof ElementInterface)
        {
            throw new Exception\InvalidArgumentException(sprintf('%s expects an array or Zend\Form\ElementInterface instance; received "%s"', __METHOD__, (is_object($attributesOrElement) ? get_class($attributesOrElement) : gettype($attributesOrElement))));
        }

        $id = $this->getId($attributesOrElement);

        if (null === $id)
        {
            throw new Exception\DomainException(sprintf('%s expects the Element provided to have either a name or an id present; neither found', __METHOD__));
        }

        $labelAttributes = [];

        if ($attributesOrElement instanceof LabelAwareInterface)
        {
            $labelAttributes = $attributesOrElement->getLabelAttributes();
        }

        $attributes = ['for' => $id];

        if (! empty($labelAttributes))
        {
            $attributes = array_merge($labelAttributes, $attributes);
        }

        $attributes = $this->createAttributesString($env, $attributes);

        return sprintf('<label %s>', $attributes);
    }

    /**
     * Return a closing label.
     *
     * @return string
     */
    public function closeTag()
    {
        return '</label>';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-label';
    }
}
