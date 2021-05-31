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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class NewdaysType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Game days per calendar day
            ->add('daysperday', RangeType::class, [
                'required' => false,
                'label'    => 'newdays.daysperday',
                'attr'     => [
                    'min' => 1,
                    'max' => 24,
                ],
                'empty_data' => 1,
            ])
            // Extra daily uses in specialty area
            ->add('specialtybonus', RangeType::class, [
                'required' => false,
                'label'    => 'newdays.specialtybonus',
                'attr'     => [
                    'min' => 0,
                    'max' => 5,
                ],
                'empty_data' => 1,
            ])
            // Modify (+ or -) the number of turns deducted after a resurrection as an absolute (number) or relative (number followed by %)
            ->add('resurrectionturns', TextType::class, [
                'required'    => false,
                'label'       => 'newdays.resurrectionturns',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 255]),
                ],
            ])
            // What weapon is standard for new players or players who just killed the dragon?
            ->add('startweapon', TextType::class, [
                'required'    => false,
                'label'       => 'newdays.startweapon',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 255]),
                ],
            ])
            // What armor is standard for new players or players who just killed the dragon?
            ->add('startarmor', TextType::class, [
                'required'    => false,
                'label'       => 'newdays.startarmor',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 255]),
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
