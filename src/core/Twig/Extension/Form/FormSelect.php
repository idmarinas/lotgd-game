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
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select as SelectElement;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormHidden;
use Zend\Stdlib\ArrayUtils;

class FormSelect extends AbstractElement
{
    /**
     * Attributes valid for the current tag.
     *
     * Will vary based on whether a select, option, or optgroup is being rendered
     *
     * @var array
     */
    protected $validTagAttributes;

    /**
     * Attributes valid for select.
     *
     * @var array
     */
    protected $validSelectAttributes = [
        'name' => true,
        'autocomplete' => true,
        'autofocus' => true,
        'disabled' => true,
        'form' => true,
        'multiple' => true,
        'required' => true,
        'size' => true
    ];

    /**
     * Attributes valid for options.
     *
     * @var array
     */
    protected $validOptionAttributes = [
        'disabled' => true,
        'selected' => true,
        'label' => true,
        'value' => true,
    ];

    /**
     * Attributes valid for option groups.
     *
     * @var array
     */
    protected $validOptgroupAttributes = [
        'disabled' => true,
        'label' => true,
    ];

    protected $translatableAttributes = [
        'label' => true,
    ];

    /**
     * @var FormHidden|null
     */
    protected $formHiddenHelper;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('laminas_form_select', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render a form <select> element from the provided $element.
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     *
     * @return string
     */
    public function render(Environment $env, ElementInterface $element)
    {
        if (! $element instanceof SelectElement)
        {
            throw new Exception\InvalidArgumentException(sprintf('%s requires that the element is of type Zend\Form\Element\Select', __METHOD__));
        }

        $name = $element->getName();

        if (empty($name) && 0 !== $name)
        {
            throw new Exception\DomainException(sprintf('%s requires that the element has an assigned name; none discovered', __METHOD__));
        }

        $translator = $this->getTranslator();
        $options = $element->getValueOptions();

        if (null !== ($emptyOption = $element->getEmptyOption()))
        {
            $options = ['' => $translator->translate($emptyOption, $this->getTranslatorTextDomain())] + $options;
        }

        $element->setAttribute('class', 'ui lotgd dropdown '.$element->getAttribute('class'));
        $attributes = $element->getAttributes();
        $value = $this->validateMultiValue($element->getValue(), $attributes);

        $attributes['name'] = $name;

        if (array_key_exists('multiple', $attributes) && $attributes['multiple'])
        {
            $attributes['name'] .= '[]';
        }
        $this->validTagAttributes = $this->validSelectAttributes;

        $rendered = sprintf(
            '<select %s>%s</select>',
            $this->createAttributesString($env, $attributes),
            $this->renderOptions($env, $options, $value)
        );

        // Render hidden element
        $useHiddenElement = method_exists($element, 'useHiddenElement')
            && method_exists($element, 'getUnselectedValue')
            && $element->useHiddenElement();

        if ($useHiddenElement)
        {
            $rendered = $this->renderHiddenElement($element).$rendered;
        }

        return $rendered;
    }

    /**
     * Render an array of options.
     *
     * Individual options should be of the form:
     *
     * <code>
     * array(
     *     'value'    => 'value',
     *     'label'    => 'label',
     *     'disabled' => $booleanFlag,
     *     'selected' => $booleanFlag,
     * )
     * </code>
     *
     * @param array $selectedOptions Option values that should be marked as selected
     *
     * @return string
     */
    public function renderOptions($env, array $options, array $selectedOptions = [])
    {
        $template = '<option %s>%s</option>';
        $optionStrings = [];
        $escapeHtml = $env->getFilter('escape')->getCallable();

        foreach ($options as $key => $optionSpec)
        {
            if (is_scalar($optionSpec))
            {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key
                ];
            }

            if (isset($optionSpec['options']) && is_array($optionSpec['options']))
            {
                $optionStrings[] = $this->renderOptgroup($env, $optionSpec, $selectedOptions);
                continue;
            }

            $value = $optionSpec['value'] ?? '';
            $label = $optionSpec['label'] ?? '';
            $selected = $optionSpec['selected'] ?? false;
            $disabled = $optionSpec['disabled'] ?? false;

            if (ArrayUtils::inArray($value, $selectedOptions))
            {
                $selected = true;
            }

            if (null !== ($translator = $this->getTranslator()))
            {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            $attributes = compact('value', 'selected', 'disabled');

            if (isset($optionSpec['attributes']) && is_array($optionSpec['attributes']))
            {
                $attributes = array_merge($attributes, $optionSpec['attributes']);
            }

            $this->validTagAttributes = $this->validOptionAttributes;
            $optionStrings[] = sprintf(
                $template,
                $this->createAttributesString($env, $attributes),
                $escapeHtml($env, $label, 'html')
            );
        }

        return implode("\n", $optionStrings);
    }

    /**
     * Render an optgroup.
     *
     * See {@link renderOptions()} for the options specification. Basically,
     * an optgroup is simply an option that has an additional "options" key
     * with an array following the specification for renderOptions().
     *
     * @return string
     */
    public function renderOptgroup($env, array $optgroup, array $selectedOptions = [])
    {
        $template = '<optgroup%s>%s</optgroup>';

        $options = [];

        if (isset($optgroup['options']) && is_array($optgroup['options']))
        {
            $options = $optgroup['options'];
            unset($optgroup['options']);
        }

        $this->validTagAttributes = $this->validOptgroupAttributes;
        $attributes = $this->createAttributesString($env, $optgroup);

        if (! empty($attributes))
        {
            $attributes = ' '.$attributes;
        }

        return sprintf(
            $template,
            $attributes,
            $this->renderOptions($options, $selectedOptions)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-select';
    }

    /**
     * Ensure that the value is set appropriately.
     *
     * If the element's value attribute is an array, but there is no multiple
     * attribute, or that attribute does not evaluate to true, then we have
     * a domain issue -- you cannot have multiple options selected unless the
     * multiple attribute is present and enabled.
     *
     * @param mixed $value
     *
     * @return array
     *
     * @throws Exception\DomainException
     */
    protected function validateMultiValue($value, array $attributes)
    {
        if (null === $value)
        {
            return [];
        }

        if (is_string($value))
        {
            return explode(',', $value);
        }
        elseif (! is_array($value))
        {
            return [$value];
        }

        if (! isset($attributes['multiple']) || ! $attributes['multiple'])
        {
            throw new Exception\DomainException(sprintf('%s does not allow specifying multiple selected values when the element does not have a multiple '.'attribute set to a boolean true', __CLASS__));
        }

        return $value;
    }

    protected function renderHiddenElement(ElementInterface $element)
    {
        $hiddenElement = new Hidden($element->getName());
        $hiddenElement->setValue($element->getUnselectedValue());

        return $this->getFormHiddenHelper()->__invoke($hiddenElement);
    }

    /**
     * @return FormHidden
     */
    protected function getFormHiddenHelper()
    {
        if (! $this->formHiddenHelper instanceof FormHidden)
        {
            $this->formHiddenHelper = new FormHidden();
        }

        return $this->formHiddenHelper;
    }
}
