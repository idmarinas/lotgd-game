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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutocompleteType extends TextareaType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['stimulus_controller'] = (string) $options['stimulus_controller'];
        $view->vars['url_value']           = (string) $options['url_value'];
        $view->vars['min_length_value']    = (int) $options['min_length_value'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'stimulus_controller' => 'autocomplete',
            'url_value'           => null,
            'min_length_value'    => 3,
        ]);

        return $resolver;
    }

    public function getBlockPrefix(): string
    {
        return 'autocomplete';
    }

    public function getName()
    {
        return 'autocomplete_field';
    }
}
