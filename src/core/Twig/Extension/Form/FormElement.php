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

class FormElement extends AbstractElement
{
    const DEFAULT_HELPER = FormInput::class;

    /**
     * Instance map to view helper.
     *
     * @var array
     */
    protected $classMap = [
        'Zend\Form\Element\Button' => FormButton::class,
        // 'Zend\Form\Element\Captcha' => FormCaptcha::class,
        'Zend\Form\Element\Csrf' => FormHidden::class,
        'Zend\Form\Element\Collection' => FormCollection::class,
        // 'Zend\Form\Element\DateTimeSelect' => FormDateTimeSelect::class,
        // 'Zend\Form\Element\DateSelect' => FormDateSelect::class,
        // 'Zend\Form\Element\MonthSelect' => FormMonthSelect::class,

        //-- Custom elements of LoTGD
        'Lotgd\Core\Form\Element\Tagify' => FormTagify::class,
        'Lotgd\Core\Form\Element\BitField' => FormBitField::class,
    ];

    /**
     * Type map to view helper.
     *
     * @var array
     */
    protected $typeMap = [
        'checkbox' => FormCheckbox::class,
        'color' => 'FormColor',
        'date' => 'FormDate',
        'datetime' => 'Form_datetime',
        'datetime-local' => 'Form_date_time_local',
        'email' => FormEmail::class,
        'file' => 'Form_file',
        'hidden' => FormHidden::class,
        'image' => 'Form_image',
        'month' => 'Form_month',
        // 'multi_checkbox' => FormMultiCheckbox::class, //-- Not use mult-checkbox use select multi
        'number' => FormNumber::class,
        'password' => 'Form_password',
        'radio' => 'Form_radio',
        'range' => FormRange::class,
        'reset' => FormReset::class,
        'search' => 'Form_search',
        'select' => FormSelect::class,
        'submit' => FormSubmit::class,
        'tel' => 'Form_tel',
        'text' => FormText::class,
        'textarea' => FormTextarea::class,
        'time' => 'Form_time',
        'url' => 'Form_url',
        'week' => 'Form_week',
    ];

    /**
     * Default helper name.
     *
     * @var string
     */
    protected $defaultHelper = self::DEFAULT_HELPER;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_element', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render an element.
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @return string
     */
    public function render(Environment $env, ElementInterface $element, ?string $translatorTextDomain = null)
    {
        $type = $this->getInstanceType($element);
        $type = $type ?: $this->getType($element);
        $type = $type ?: $this->defaultHelper;

        $translatorTextDomain = $translatorTextDomain ?: $this->getTranslatorTextDomain();

        $render = $env->getExtension($type);
        $render->setTranslatorTextDomain($element->getOption('translator_text_domain') ?: $translatorTextDomain);

        return $render->render($env, $element);
    }

    /**
     * Set default helper name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setDefaultHelper($name)
    {
        $this->defaultHelper = $name;

        return $this;
    }

    /**
     * Add form element type to plugin map.
     *
     * @param string $type
     * @param string $plugin
     *
     * @return self
     */
    public function addType($type, $plugin)
    {
        $this->typeMap[$type] = $plugin;

        return $this;
    }

    /**
     * Add instance class to plugin map.
     *
     * @param string $class
     * @param string $plugin
     *
     * @return self
     */
    public function addClass($class, $plugin)
    {
        $this->classMap[$class] = $plugin;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-element';
    }

    /**
     * Render element by instance map.
     */
    protected function getInstanceType(ElementInterface $element): ?string
    {
        foreach ($this->classMap as $class => $pluginName)
        {
            if ($element instanceof $class)
            {
                return $pluginName;
            }
        }

        return null;
    }

    /**
     * Render element by type map.
     */
    protected function getType(ElementInterface $element): ?string
    {
        $type = $element->getAttribute('type');

        return $this->typeMap[$type] ?? null;
    }
}
