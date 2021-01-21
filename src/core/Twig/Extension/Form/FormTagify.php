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

class FormTagify extends FormInput
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('laminas_form_tagify', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render the tag element.
     */
    public function render(Environment $env, ElementInterface $element): string
    {
        return $env->render('{theme}/form/element/tagify.html.twig', [
            'element' => $element,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-tagify';
    }

    /**
     * Determine input type to use.
     *
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return '';
    }
}
