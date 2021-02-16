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

namespace Lotgd\Core\Form\AboutType;

use Lotgd\Core\Form\Type\ViewOnlyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Forest Fights per day
            ->add('turns', ViewOnlyType::class, [
                'label'        => 'forest.turns',
                'apply_filter' => 'numeral',
            ])
            //-- Forest Creatures always drop at least 1/4 of possible gold
            ->add('dropmingold', ViewOnlyType::class, [
                'label'        => 'forest.dropmingold',
                'apply_filter' => 'yes_no',
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
