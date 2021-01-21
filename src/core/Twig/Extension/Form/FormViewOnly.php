<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Twig\Extension\Form;

use Laminas\Form\ElementInterface;
use Twig\Environment as Environment;
use Twig\TwigFunction;

class FormViewOnly extends FormInput
{
    /**
     * Attributes valid for the input tag.
     *
     * @var array
     */
    protected $validTagAttributes = [
        'id'     => true,
        'alt'    => true,
        'height' => true,
        'width'  => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('laminas_form_view_only', [$this, 'render'], ['needs_environment' => true]),
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
        $attributes          = $element->getAttributes();
        $attributes['class'] = 'ui small label '.($attributes['class'] ?? '');

        $filterName = $element->getOption('apply_filter');
        $uncolorize = $env->getFilter('uncolorize')->getCallable();
        $value      = $element->getValue();

        if ((\is_array($filterName) && isset($filterName['name'])) || (\is_string($filterName) && $filterName))
        {
            $filterName = ($filterName['name'] ?? '') ?: $filterName;
            $filter     = $env->getFilter($filterName)->getCallable();

            $value = $filter($value);

            if (isset($filterName['params']) && \is_array($filterName['params']))
            {
                $value = $filter($value, ...$filterName['params']);
            }
        }

        return \sprintf(
            '<span %s>%s</span>',
            $this->createAttributesString($env, $attributes),
            $uncolorize($value)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-view-only';
    }

    /**
     * Determine input type to use.
     *
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'text';
    }
}
