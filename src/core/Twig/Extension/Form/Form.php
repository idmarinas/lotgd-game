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
use Zend\Form\Element\Csrf;
use Zend\Form\Factory;
use Zend\Form\FormInterface;

class Form extends AbstractElement
{
    use \Lotgd\Core\Pattern\Navigation;

    /**
     * Attributes valid for forms.
     *
     * @var array
     */
    protected $validTagAttributes = [
        'accept-charset' => true,
        'action' => true,
        'autocomplete' => true,
        'enctype' => true,
        'method' => true,
        'name' => true,
        'novalidate' => true,
        'target' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form', [$this, 'formRender'], ['needs_environment' => true]),
            new TwigFunction('form_tabed', [$this, 'formRenderTab'], ['needs_environment' => true]),
            new TwigFunction('form_open_tag', [$this, 'openTag'], ['needs_environment' => true]),
            new TwigFunction('form_close_tag', [$this, 'closeTag'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render a form from.
     */
    public function formRender(Environment $env, FormInterface $form): string
    {
        $params = $this->getFormParams($form);

        return $env->renderThemeTemplate('form/form.twig', $params);
    }

    /**
     * Render a tabed form, this option not prepare form.
     * Not append the name of the fieldsets to every elements.
     * NOTICE: This method not avoid name clashes if the same fieldset is used multiple times.
     */
    public function formRenderTab(Environment $env, FormInterface $form): string
    {
        $params = $this->getFormParams($form);

        return $env->renderThemeTemplate('form/tabed.twig', $params);
    }

    /**
     * Generate an opening tag.
     */
    public function openTag(Environment $env, FormInterface $form = null): string
    {
        //-- Add form action url to allowed navs
        if ($form->getAttribute('action'))
        {
            $this->getNavigation()->addNavAllow($form->getAttribute('action'));
        }

        $attributes = [
            'action' => '',
            'method' => 'get',
        ];

        $options = $form->getOptions();
        $this->setTranslatorTextDomain($options['translator_text_domain'] ?? 'default');

        if ($form instanceof FormInterface)
        {
            $formAttributes = $form->getAttributes();

            if (! array_key_exists('id', $formAttributes) && array_key_exists('name', $formAttributes))
            {
                $formAttributes['id'] = $formAttributes['name'];
            }

            $attributes = array_merge($attributes, $formAttributes);
        }

        if ($attributes)
        {
            return sprintf('<form %s>', $this->createAttributesString($env, $attributes));
        }

        return '<form>';
    }

    /**
     * Generate a closing tag.
     */
    public function closeTag(): string
    {
        return '</form>';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-render';
    }

    /**
     * Prepare params for form.
     */
    private function getFormParams(FormInterface $form): array
    {
        //-- Prepare form for show
        if (method_exists($form, 'prepare'))
        {
            $form->prepare();
        }

        //-- Addd error class to show errors of form
        if (! empty($form->getMessages()))
        {
            $form->setAttribute('class', $form->getAttribute('class').' error');
        }

        $security = $form->getOption('use_csrf_security');

        //-- Add security element
        if ($security || null === $security)
        {
            $security = new Csrf('security');
        }

        $buttonsRaw = $form->getOption('buttons');
        $buttons = $buttonsRaw ? $this->generateButtonsForForm($buttonsRaw) : [];

        return [
            'form' => $form,
            'security' => $security,
            'buttons' => $buttons,
            'translatorTextDomain' => $form->getOptions()['translator_text_domain'] ?? 'default'
        ];
    }

    /**
     * Generate buttons for this form if necesary.
     *
     * @return void
     */
    private function generateButtonsForForm(array $buttonsRaw)
    {
        $buttons = [];
        $factory = new Factory();

        //-- Default is always added submit button
        if (null === ($buttonsRaw['submit'] ?? null) || $buttonsRaw['submit'])
        {
            $options = is_array($buttonsRaw['submit']) ? $buttonsRaw['submit'] : [];
            $options = array_merge([
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => [
                    'id' => 'button-submit',
                    'class' => 'ui primary button'
                ],
                'options' => [
                    'label' => 'button.submit',
                    'translator_text_domain' => 'app-form'
                ]
            ], $options);

            $buttons['submit'] = $factory->createElement($options);
        }

        //-- Default is not added reset button
        if (($buttonsRaw['reset'] ?? false) || $buttonsRaw['reset'])
        {
            $options = is_array($buttonsRaw['reset']) ? $buttonsRaw['reset'] : [];
            $options = array_merge([
                'name' => 'reset',
                'type' => 'Button',
                'attributes' => [
                    'type' => 'reset',
                    'id' => 'button-reset',
                    'class' => 'ui secondary button'
                ],
                'options' => [
                    'label' => 'button.reset',
                    'translator_text_domain' => 'app-form'
                ]
            ], $options);

            $buttons['reset'] = $factory->createElement($options);
        }

        unset($buttonsRaw['reset'], $buttonsRaw['submit'], $options);

        if (is_array($buttonsRaw) && ! empty($buttonsRaw))
        {
            foreach ($buttonsRaw as $index => $button)
            {
                $buttons[$index] = $factory->createElement($button);
            }
        }

        return $buttons;
    }
}
