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

use Lotgd\Core\Form\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BankType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Max forest fights remaining to earn interest?
            ->add('fightsforinterest', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min' => 0,
                    'max' => 10,
                ],
                'empty_data'  => 1,
                'label'       => 'bank.fightsforinterest',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Max Interest Rate (%)
            ->add('maxinterest', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min' => 5,
                    'max' => 10,
                ],
                'empty_data'  => 5,
                'label'       => 'bank.maxinterest',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Min Interest Rate (%)
            ->add('mininterest', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min' => 0,
                    'max' => 5,
                ],
                'empty_data'  => 1,
                'label'       => 'bank.mininterest',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Over what amount of gold does the bank cease paying interest?
            ->add('maxgoldforinterest', NumberType::class, [
                'required'    => false,
                'empty_data'  => 100000,
                'label'       => 'bank.maxgoldforinterest',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Max player can borrow per level (val * level for max)
            ->add('borrowperlevel', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 5,
                    'max'                   => 200,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 5,
                'label'       => 'bank.borrowperlevel',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Allow players to transfer gold
            ->add('allowgoldtransfer', CheckboxType::class, [
                'required' => false,
                'label'    => 'bank.allowgoldtransfer',
            ])
            // Max player can receive from a transfer (val * level)
            ->add('transferperlevel', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 5,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 5,
                'label'       => 'bank.transferperlevel',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Min level a player (0 DK's) needs to transfer gold
            ->add('mintransferlev', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min' => 1,
                    'max' => 5,
                ],
                'empty_data'  => 1,
                'label'       => 'bank.mintransferlev',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Total transfers a player can receive in one day
            ->add('transferreceive', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min' => 0,
                    'max' => 5,
                ],
                'empty_data'  => 1,
                'label'       => 'bank.transferreceive',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Amount player can transfer to others (val * level)
            ->add('maxtransferout', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 5,
                    'max'                   => 100,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 5,
                'label'       => 'bank.maxtransferout',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Fee for express inn payment (x or x%)
            ->add('innfee', NumberType::class, [
                'required'    => false,
                'empty_data'  => 0,
                'label'       => 'bank.innfee',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
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
