<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ViewOnlyType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['apply_filter'] = $options['apply_filter'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required'     => false,
            'apply_filter' => '',
            'attr'         => [
                'class' => 'inline',
            ],
        ]);

        $resolver->setAllowedTypes('apply_filter', 'string');
    }

    public function getBlockPrefix(): string
    {
        return 'view_only';
    }

    public function getName()
    {
        return 'view_only_field';
    }
}
