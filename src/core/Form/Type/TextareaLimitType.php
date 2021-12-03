<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class TextareaLimitType extends TextareaType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['characters_limit'] = (int) $options['characters_limit'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'characters_limit' => 100,
            'attr' => [
                'data-limit-characters-target' => 'input',
            ],
            'row_attr' => [
                'data-limit-characters-text-chars-left-value' => 'parts.characters_limit.left',
                'data-limit-characters-text-chars-over-value' => 'parts.characters_limit.over',
            ]
        ]);

        return $resolver;
    }

    public function getBlockPrefix(): string
    {
        return 'textarea_limit';
    }

    public function getName()
    {
        return 'textarea_limit_field';
    }
}
