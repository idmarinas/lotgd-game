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

namespace Lotgd\Core\Form\ConfigurationType;

use Lotgd\Core\Form\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CombatType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('autofight', CheckboxType::class, [
                'required' => false,
                'label'    => 'combat.autofight',
            ])
            ->add('autofightfull', ChoiceType::class, [
                'required' => false,
                'choices'  => [
                    'combat.autofightfull.option.never'  => 0,
                    'combat.autofightfull.option.always' => 1,
                    'combat.autofightfull.option.flee'   => 2,
                ],
                'empty_data' => 0,
                'label'      => 'combat.autofightfull.label',
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'form-core-grotto-configuration',
        ]);
    }
}
