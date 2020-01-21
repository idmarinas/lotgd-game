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

class FormTags extends FormInput
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('form_tags', [$this, 'render'], ['needs_environment' => true]),
        ];
    }

    /**
     * Render the tag element.
     *
     * @param Environment $env
     * @param ElementInterface $element
     * @return string
     */
    public function render(Environment $env, ElementInterface $element): string
    {
        return $env->renderThemeTemplate('form/element/tags.twig', [
            'element' => $element,
        ]);
    }

    /**
     * Determine input type to use
     *
     * @param  ElementInterface $element
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form-tags';
    }
}
