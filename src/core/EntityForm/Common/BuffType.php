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

namespace Lotgd\Core\EntityForm\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuffType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
                'required' => false
            ])
            ->add('roundmsg', TextType::class, [
                'label' => 'roundmsg',
                'required' => false
            ])
            ->add('wearoff', TextType::class, [
                'label' => 'wearoff',
                'required' => false
            ])
            ->add('effectmsg', TextType::class, [
                'label' => 'effectmsg',
                'required' => false
            ])
            ->add('effectnodmgmsg', TextType::class, [
                'label' => 'effectnodmgmsg',
                'required' => false
            ])
            ->add('rounds', NumberType::class, [
                'label' => 'rounds',
                'required' => false
            ])
            ->add('atkmod', NumberType::class, [
                'label' => 'atkmod',
                'attr' => [
                    'step' => 0.01
                ],
                'required' => false
            ])
            ->add('defmod', NumberType::class, [
                'label' => 'defmod',
                'attr' => [
                    'step' => 0.01
                ],
                'required' => false
            ])
            ->add('regen', NumberType::class, [
                'label' => 'regen',
                'required' => false
            ])
            ->add('minioncount', NumberType::class, [
                'label' => 'minioncount',
                'required' => false
            ])
            ->add('minbadguydamage', NumberType::class, [
                'label' => 'minbadguydamage',
                'required' => false
            ])
            ->add('maxbadguydamage', NumberType::class, [
                'label' => 'maxbadguydamage',
                'required' => false
            ])
            ->add('mingoodguydamage', NumberType::class, [
                'label' => 'mingoodguydamage',
                'required' => false
            ])
            ->add('maxgoodguydamage', NumberType::class, [
                'label' => 'maxgoodguydamage',
                'required' => false
            ])
            ->add('lifetap', NumberType::class, [
                'label' => 'lifetap',
                'required' => false
            ])
            ->add('damageshield', NumberType::class, [
                'label' => 'damageshield',
                'required' => false
            ])
            ->add('badguydmgmod', NumberType::class, [
                'label' => 'badguydmgmod',
                'required' => false
            ])
            ->add('badguyatkmod', NumberType::class, [
                'label' => 'badguyatkmod',
                'required' => false
            ])
            ->add('badguydefmod', NumberType::class, [
                'label' => 'badguydefmod',
                'required' => false
            ])

            ->add('allowinpvp', CheckboxType::class, [
                'label' => 'allowinpvp',
                'required' => false
            ])
            ->add('allowintrain', CheckboxType::class, [
                'label' => 'allowintrain',
                'required' => false
            ])
            ->add('survivenewday', CheckboxType::class, [
                'label' => 'survivenewday',
                'required' => false
            ])
            ->add('expireafterfight', CheckboxType::class, [
                'label' => 'expireafterfight',
                'required' => false
            ])
            ->add('invulnerable', CheckboxType::class, [
                'label' => 'invulnerable',
                'required' => false
            ])
        ;
    }
    // name: Buff name
    // roundmsg: Each round
    // wearoff: Wear off
    // effectmsg: Effect
    // effectnodmgmsg: Effect No Damage
    // effectfailmsg: Effect Fail
    // rounds: Rounds to last (from new day)
    // atkmod: Player Atk mod (multiplier)
    // defmod: Player Def mod (multiplier)
    // invulnerable: Player is invulnerable
    // regen: Regen
    // minioncount: Minion Count
    // minbadguydamage: Min Badguy Damage
    // maxbadguydamage: Max Badguy Damage
    // mingoodguydamage: Min Goodguy Damage
    // maxgoodguydamage: Max Goodguy Damage
    // lifetap: Lifetap (multiplier)
    // damageshield: Damage shield (multiplier)
    // badguydmgmod: Badguy Damage mod (multiplier)
    // badguyatkmod: Badguy Atk mod (multiplier)
    // badguydefmod: Badguy Def mod (multiplier)

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'form-core-grotto-buffs'
        ]);
    }
}
