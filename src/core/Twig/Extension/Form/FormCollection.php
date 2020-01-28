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
use Zend\Form\Element\Collection as CollectionElement;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Zend\View\Helper\HelperInterface;

class FormCollection extends AbstractElement
{
    /**
     * If set to true, collections are automatically wrapped around a fieldset.
     *
     * @var bool
     */
    protected $shouldWrap = true;

    /**
     * This is the default wrapper that the collection is wrapped into.
     *
     * @var string
     */
    protected $wrapper = '<fieldset%4$s>%2$s%1$s%3$s</fieldset>';

    /**
     * This is the default label-wrapper.
     *
     * @var string
     */
    protected $labelWrapper = '<legend>%s</legend>';

    /**
     * Where shall the template-data be inserted into.
     *
     * @var string
     */
    protected $templateWrapper = '<span data-template="%s"></span>';

    /**
     * The name of the default view helper that is used to render sub elements.
     *
     * @var string
     */
    protected $defaultElementHelper = FormRow::class;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_fieldset', [$this, 'fieldsetRender'], ['needs_environment' => true]),
            new TwigFunction('form_fieldset_tab', [$this, 'tabRender'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render collection as fieldset (normal form).
     */
    public function fieldsetRender(Environment $env, ElementInterface $element, ?string $translatorTextDomain = null): string
    {
        $params = $this->getCollectionParams($env, $element, $translatorTextDomain);

        return $env->renderThemeTemplate('form/element/fieldset.twig', $params);
    }

    /**
     * Render a collections as tabs menu.
     */
    public function tabRender(Environment $env, ElementInterface $element, ?string $translatorTextDomain = null, ?string $activeTab = null): string
    {
        $element->setAttribute('class', $element->getAttribute('class').'ui tab');

        if ($activeTab == $element->getAttribute('id'))
        {
            $element->setAttribute('class', $element->getAttribute('class').' active');
        }

        $params = $this->getCollectionParams($env, $element, $translatorTextDomain);

        return $env->renderThemeTemplate('form/element/fieldset/tab.twig', $params);
    }

    /**
     * Only render a template.
     *
     * @return string
     */
    public function renderTemplate(Environment $env, CollectionElement $collection)
    {
        $elementHelper = $env->getExtension(FormElement::class);
        $fieldsetHelper = $env->getExtension(FormCollection::class);
        $escapeHtmlAttribHelper = $this->getEscapeHtmlAttrHelper();

        $textDomain = $this->getTranslatorTextDomain() ?: 'default';
        $templateMarkup = '';

        $elementOrFieldset = $collection->getTemplateElement();

        if ($elementOrFieldset instanceof FieldsetInterface)
        {
            $elementOrFieldset->setShouldWrap($this->shouldWrap());
            $fieldsetHelper->setTranslatorTextDomain($elementOrFieldset->getOptions()['translator_text_domain'] ?? $textDomain);

            $templateMarkup .= $fieldsetHelper->render($env, $elementOrFieldset, $this->shouldWrap());
        }
        elseif ($elementOrFieldset instanceof ElementInterface)
        {
            $elementHelper->setTranslatorTextDomain($elementOrFieldset->getOptions()['translator_text_domain'] ?? $textDomain);

            $templateMarkup .= $elementHelper->render($env, $elementOrFieldset);
        }

        return sprintf(
            $this->getTemplateWrapper(),
            $escapeHtmlAttribHelper($templateMarkup)
        );
    }

    /**
     * If set to true, collections are automatically wrapped around a fieldset.
     *
     * @param bool $wrap
     *
     * @return FormCollection
     */
    public function setShouldWrap($wrap)
    {
        $this->shouldWrap = (bool) $wrap;

        return $this;
    }

    /**
     * Get wrapped.
     *
     * @return bool
     */
    public function shouldWrap()
    {
        return $this->shouldWrap;
    }

    /**
     * Sets the name of the view helper that should be used to render sub elements.
     *
     * @param string $defaultSubHelper the name of the view helper to set
     *
     * @return FormCollection
     */
    public function setDefaultElementHelper($defaultSubHelper)
    {
        $this->defaultElementHelper = $defaultSubHelper;

        return $this;
    }

    /**
     * Gets the name of the view helper that should be used to render sub elements.
     *
     * @return string
     */
    public function getDefaultElementHelper()
    {
        return $this->defaultElementHelper;
    }

    /**
     * Sets the element helper that should be used by this collection.
     *
     * @param HelperInterface $elementHelper the element helper to use
     *
     * @return FormCollection
     */
    public function setElementHelper(HelperInterface $elementHelper)
    {
        $this->elementHelper = $elementHelper;

        return $this;
    }

    /**
     * Sets the fieldset helper that should be used by this collection.
     *
     * @param HelperInterface $fieldsetHelper the fieldset helper to use
     *
     * @return FormCollection
     */
    public function setFieldsetHelper(HelperInterface $fieldsetHelper)
    {
        $this->fieldsetHelper = $fieldsetHelper;

        return $this;
    }

    /**
     * Get the wrapper for the collection.
     *
     * @return string
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * Set the wrapper for this collection.
     *
     * The string given will be passed through sprintf with the following three
     * replacements:
     *
     * 1. The content of the collection
     * 2. The label of the collection. If no label is given this will be an empty
     *   string
     * 3. The template span-tag. This might also be an empty string
     *
     * The preset default is <pre><fieldset>%2$s%1$s%3$s</fieldset></pre>
     *
     * @param string $wrapper
     *
     * @return self
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * Set the label-wrapper
     * The string will be passed through sprintf with the label as single
     * parameter
     * This defaults to '<legend>%s</legend>'.
     *
     * @param string $labelWrapper
     *
     * @return self
     */
    public function setLabelWrapper($labelWrapper)
    {
        $this->labelWrapper = $labelWrapper;

        return $this;
    }

    /**
     * Get the wrapper for the label.
     *
     * @return string
     */
    public function getLabelWrapper()
    {
        return $this->labelWrapper;
    }

    /**
     * Ge the wrapper for the template.
     *
     * @return string
     */
    public function getTemplateWrapper()
    {
        return $this->templateWrapper;
    }

    /**
     * Set the string where the template will be inserted into.
     *
     * This string will be passed through sprintf and has the template as single
     * parameter
     *
     * THis defaults to '<span data-template="%s"></span>'
     *
     * @param string $templateWrapper
     *
     * @return self
     */
    public function setTemplateWrapper($templateWrapper)
    {
        $this->templateWrapper = $templateWrapper;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-collection';
    }

    private function getCollectionParams(Environment $env, ElementInterface $element, ?string $translatorTextDomain = null): array
    {
        if ($element instanceof CollectionElement && $element->shouldCreateTemplate())
        {
            $templateMarkup = $this->renderTemplate($env, $element);
        }

        $attributes = $element->getAttributes();

        unset($attributes['name']);
        $attributesString = $attributes ? ' '.$this->createAttributesString($env, $attributes) : '';

        return [
            'collection' => $element,
            'template' => $templateMarkup ?? '',
            'shouldWrap' => $this->shouldWrap,
            'attributesString' => $attributesString,
            'translatorTextDomain' => $translatorTextDomain
        ];
    }
}
