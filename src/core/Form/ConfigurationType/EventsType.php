<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\ConfigurationType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EventsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Chance for Something Special in the Forest
            ->add('forestchance', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 10,
                'label'       => 'events.forestchance',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // Chance for Something Special in any village
            ->add('villagechance', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 5,
                'label'       => 'events.villagechance',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // Chance for Something Special in the Inn
            ->add('innchance', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 5,
                'label'       => 'events.innchance',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // Chance for Something Special in the Graveyard
            ->add('gravechance', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 10,
                'label'       => 'events.gravechance',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // Chance for Something Special in the Gardens
            ->add('gardenchance', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 5,
                'label'       => 'events.gardenchance',
                'constraints' => [new Assert\DivisibleBy(1)],
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
