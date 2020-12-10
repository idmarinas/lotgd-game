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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PvpType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Enable Slay Other Players
            ->add('pvp', CheckboxType::class, [
                'required' => false,
                'label'    => 'pvp.pvp',
            ])
            // Timeout in seconds to wait after a player was PvP'd
            ->add('pvptimeout', NumberType::class, [
                'required'    => false,
                'label'       => 'pvp.pvptimeout',
                'empty_data'  => 900,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\Positive(),
                ],
            ])
            // Player Fights per day
            ->add('pvpday', RangeType::class, [
                'required' => false,
                'label'    => 'pvp.pvpday',
                'attr'     => [
                    'min' => 1,
                    'max' => 10,
                ],
                'empty_data' => 1,
            ])
            // Can players be engaged in pvp after a DK until they visit the village again?
            ->add('pvpdragonoptout', CheckboxType::class, [
                'required' => false,
                'label'    => 'pvp.pvpdragonoptout',
            ])
            // How many levels can attacker & defender be different? (-1=any - lower limit is always +1)
            ->add('pvprange', RangeType::class, [
                'required' => false,
                'label'    => 'pvp.pvprange.label',
                'help'     => 'pvp.pvprange.note',
                'attr'     => [
                    'min' => -1,
                    'max' => 15,
                ],
                'empty_data' => 1,
            ])
            // Days that new players are safe from PvP
            ->add('pvpimmunity', RangeType::class, [
                'required' => false,
                'label'    => 'pvp.pvpimmunity',
                'attr'     => [
                    'min' => 1,
                    'max' => 5,
                ],
                'empty_data' => 1,
            ])
            // Experience below which player is safe from PvP
            ->add('pvpminexp', NumberType::class, [
                'required'    => false,
                'label'       => 'pvp.pvpminexp',
                'empty_data'  => 1500,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Percent of victim experience attacker gains on win
            ->add('pvpattgain', RangeType::class, [
                'required' => false,
                'label'    => 'pvp.pvpattgain',
                'attr'     => [
                    'min'                   => 0.25,
                    'max'                   => 20,
                    'step'                  => 0.25,
                    'disable_slider_labels' => true,
                ],
                'empty_data' => 0.25,
            ])
            // Percent of experience attacker loses on loss
            ->add('pvpattlose', RangeType::class, [
                'required' => false,
                'label'    => 'pvp.pvpattlose',
                'attr'     => [
                    'min'                   => 0.25,
                    'max'                   => 20,
                    'step'                  => 0.25,
                    'disable_slider_labels' => true,
                ],
                'empty_data' => 0.25,
            ])
            // Percent of attacker experience defender gains on win
            ->add('pvpdefgain', RangeType::class, [
                'required' => false,
                'label'    => 'pvp.pvpdefgain',
                'attr'     => [
                    'min'                   => 0.25,
                    'max'                   => 20,
                    'step'                  => 0.25,
                    'disable_slider_labels' => true,
                ],
                'empty_data' => 0.25,
            ])
            // Percent of experience defender loses on loss
            ->add('pvpdeflose', RangeType::class, [
                'required' => false,
                'label'    => 'pvp.pvpdeflose',
                'attr'     => [
                    'min'                   => 0.25,
                    'max'                   => 20,
                    'step'                  => 0.25,
                    'disable_slider_labels' => true,
                ],
                'empty_data' => 0.25,
            ])
            // Is the maximum amount a successful attacker or defender can gain limited?
            ->add('pvphardlimit', CheckboxType::class, [
                'required' => false,
                'label'    => 'pvp.pvphardlimit',
            ])
            // If yes - What is the maximum amount of EXP he can get?
            ->add('pvphardlimitamount', NumberType::class, [
                'required'    => false,
                'label'       => 'pvp.pvphardlimitamount',
                'empty_data'  => 0,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Can players attack others with same ID?
            ->add('pvpsameid', CheckboxType::class, [
                'required' => false,
                'label'    => 'pvp.pvpsameid',
            ])
            // Can players attack others with same IP?
            ->add('pvpsameip', CheckboxType::class, [
                'required' => false,
                'label'    => 'pvp.pvpsameip',
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
