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
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EnemiesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Multiple monsters will attack players above which amount of dragonkills?
            ->add('multifightdk', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min' => 8,
                    'max' => 50,
                ],
                'empty_data'  => 8,
                'label'       => 'enemies.multifightdk',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // The chance for an attack from multiple enemies is
            ->add('multichance', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 30,
                'label'       => 'enemies.multichance',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // Can one creature in the creature table appear in a pack (all monsters you encounter in that fight are duplicates of this?
            ->add('allowpackmonsters', CheckboxType::class, [
                'required' => false,
                'label'    => 'enemies.allowpackmonsters',
            ])
            // Need Multiple Enemies to be from a different category (sanity reasons)?
            ->add('multicategory', CheckboxType::class, [
                'required' => false,
                'label'    => 'enemies.multicategory',
            ])
            // Additional experience (%) per enemy during multifights?
            ->add('addexp', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 15,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 1,
                'label'       => 'enemies.addexp',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // How many enemies will attack per round (max. value)
            ->add('maxattacks', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 1,
                    'max'                   => 15,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 1,
                'label'       => 'enemies.maxattacks.label',
                'help'        => 'enemies.maxattacks.note',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // During multi-fights hand out experience instantly?
            ->add('instantexp', CheckboxType::class, [
                'required' => false,
                'label'    => 'enemies.instantexp',
            ])
            // The base number of multiple enemies at minimum is
            ->add('multibasemin', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 50,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 2,
                'label'       => 'enemies.multibasemin',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // The base number of multiple enemies at maximum is
            ->add('multibasemax', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 50,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 3,
                'label'       => 'enemies.multibasemax',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // The number of multiple enemies at minimum for slumming is
            ->add('multislummin', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 50,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 1,
                'label'       => 'enemies.multislummin',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // The number of multiple enemies at maximum for slumming is
            ->add('multislummax', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 50,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 2,
                'label'       => 'enemies.multislummax',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // The number of multiple enemies at minimum for thrill seeking is
            ->add('multithrillmin', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 50,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 3,
                'label'       => 'enemies.multithrillmin',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // The number of multiple enemies at maximum for thrill seeking is
            ->add('multithrillmax', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 50,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 4,
                'label'       => 'enemies.multithrillmax',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // The number of multiple enemies at minimum for suicide is
            ->add('multisuimin', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 50,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 4,
                'label'       => 'enemies.multisuimin',
                'constraints' => [new Assert\DivisibleBy(1)],
            ])
            // The number of multiple enemies at maximum for suicide is
            ->add('multisuimax', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 0,
                    'max'                   => 50,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 5,
                'label'       => 'enemies.multisuimax',
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
