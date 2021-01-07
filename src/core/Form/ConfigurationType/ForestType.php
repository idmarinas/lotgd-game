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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ForestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Forest Fights per day
            ->add('turns', RangeType::class, [
                'required' => false,
                'label'    => 'forest.turns',
                'attr'     => [
                    'min' => 5,
                    'max' => 30,
                ],
                'empty_data' => 5,
            ])
            // Forest Creatures show health
            ->add('forestcreaturebar', ChoiceType::class, [
                'required' => false,
                'choices'  => [
                    'forest.forestcreaturebar.option.text'    => 0,
                    'forest.forestcreaturebar.option.bar'     => 1,
                    'forest.forestcreaturebar.option.textbar' => 2,
                ],
                'label' => 'forest.forestcreaturebar.label',
                'help'  => 'forest.forestcreaturebar.note',
            ])
            // Forest Creatures drop at least 1/4 of max gold
            ->add('dropmingold', CheckboxType::class, [
                'required' => false,
                'label'    => 'forest.dropmingold',
            ])
            // Allow players to Seek Suicidally?
            ->add('suicide', CheckboxType::class, [
                'required' => false,
                'label'    => 'forest.suicide',
            ])
            // Minimum DKs before players can Seek Suicidally?
            ->add('suicidedk', NumberType::class, [
                'required'    => false,
                'label'       => 'forest.suicidedk.label',
                'help'        => 'forest.suicidedk.note',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // In one out of how many fight rounds do enemies do a power attack?
            ->add('forestpowerattackchance', RangeType::class, [
                'required' => false,
                'label'    => 'forest.forestpowerattackchance',
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data' => 0,
            ])
            // Multiplier for the power attack
            ->add('forestpowerattackmulti', RangeType::class, [
                'required' => false,
                'label'    => 'forest.forestpowerattackmulti',
                'attr'     => [
                    'min'                   => 1,
                    'max'                   => 10,
                    'step'                  => 0.1,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 1.5,
                'constraints' => [
                    new Assert\DivisibleBy(0.1),
                    new Assert\Positive(),
                    new Assert\Range(['min' => 1, 'max' => 10]),
                ],
            ])
            // Player will find a gem one in X times
            ->add('forestgemchance', RangeType::class, [
                'required' => false,
                'label'    => 'forest.forestgemchance',
                'attr'     => [
                    'min'                   => 10,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data' => 10,
            ])
            // Should monsters which get buffed with extra HP/Att/Def get a gold+exp bonus?
            ->add('disablebonuses', CheckboxType::class, [
                'required' => false,
                'label'    => 'forest.disablebonuses',
            ])
            // What percentage of experience should be lost?
            ->add('forestexploss', RangeType::class, [
                'required' => false,
                'label'    => 'forest.forestexploss',
                'attr'     => [
                    'min'                   => 10,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data' => 10,
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'form_core_configuration',
        ]);
    }
}
