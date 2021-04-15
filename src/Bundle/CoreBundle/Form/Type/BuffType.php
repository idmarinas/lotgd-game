<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType as SymfonyNumberType;

class BuffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'name',
                'required' => false,
            ])
            ->add('schema', TextType::class, [
                'label'    => 'schema',
                'required' => false,
            ])
            ->add('roundmsg', TextType::class, [
                'label'    => 'roundmsg',
                'required' => false,
            ])
            ->add('wearoff', TextType::class, [
                'label'    => 'wearoff',
                'required' => false,
            ])
            ->add('effectmsg', TextType::class, [
                'label'    => 'effectmsg',
                'required' => false,
            ])
            ->add('effectnodmgmsg', TextType::class, [
                'label'    => 'effectnodmgmsg',
                'required' => false,
            ])
            ->add('rounds', NumberType::class, [
                'label'    => 'rounds',
                'required' => false,
            ])
            ->add('atkmod', SymfonyNumberType::class, [
                'label' => 'atkmod',
                'attr'  => [
                    'step' => 0.01,
                ],
                'required' => false,
            ])
            ->add('defmod', SymfonyNumberType::class, [
                'label' => 'defmod',
                'attr'  => [
                    'step' => 0.01,
                ],
                'required' => false,
            ])
            ->add('regen', SymfonyNumberType::class, [
                'label'    => 'regen',
                'required' => false,
            ])
            ->add('minioncount', SymfonyNumberType::class, [
                'label'    => 'minioncount',
                'required' => false,
            ])
            ->add('minbadguydamage', SymfonyNumberType::class, [
                'label'    => 'minbadguydamage',
                'required' => false,
            ])
            ->add('maxbadguydamage', SymfonyNumberType::class, [
                'label'    => 'maxbadguydamage',
                'required' => false,
            ])
            ->add('mingoodguydamage', SymfonyNumberType::class, [
                'label'    => 'mingoodguydamage',
                'required' => false,
            ])
            ->add('maxgoodguydamage', SymfonyNumberType::class, [
                'label'    => 'maxgoodguydamage',
                'required' => false,
            ])
            ->add('lifetap', SymfonyNumberType::class, [
                'label'    => 'lifetap',
                'required' => false,
            ])
            ->add('damageshield', SymfonyNumberType::class, [
                'label'    => 'damageshield',
                'required' => false,
            ])
            ->add('badguydmgmod', SymfonyNumberType::class, [
                'label'    => 'badguydmgmod',
                'required' => false,
            ])
            ->add('badguyatkmod', SymfonyNumberType::class, [
                'label'    => 'badguyatkmod',
                'required' => false,
            ])
            ->add('badguydefmod', SymfonyNumberType::class, [
                'label'    => 'badguydefmod',
                'required' => false,
            ])

            ->add('allowinpvp', CheckboxType::class, [
                'label'    => 'allowinpvp',
                'required' => false,
            ])
            ->add('allowintrain', CheckboxType::class, [
                'label'    => 'allowintrain',
                'required' => false,
            ])
            ->add('survivenewday', CheckboxType::class, [
                'label'    => 'survivenewday',
                'required' => false,
            ])
            ->add('expireafterfight', CheckboxType::class, [
                'label'    => 'expireafterfight',
                'required' => false,
            ])
            ->add('invulnerable', CheckboxType::class, [
                'label'    => 'invulnerable',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'lotgd_core_form_buff_type',
        ]);
    }
}
