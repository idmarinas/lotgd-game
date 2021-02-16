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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DaysetupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Show the village game time in what format?
            ->add('gametime', TextType::class, [
                'required'    => false,
                'label'       => 'daysetup.gametime.label',
                'help'        => 'daysetup.gametime.note',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 100]),
                ],
            ])
            // Day Duration
            ->add('dayduration', TextType::class, [
                'required' => false,
                'disabled' => true,
                'label'    => 'daysetup.dayduration',
            ])
            // Current game time
            ->add('curgametime', TextType::class, [
                'required' => false,
                'disabled' => true,
                'label'    => 'daysetup.curgametime',
            ])
            // Current Server Time
            ->add('curservertime', TextType::class, [
                'required' => false,
                'disabled' => true,
                'label'    => 'daysetup.curservertime',
            ])
            // Last new day
            ->add('lastnewday', TextType::class, [
                'required' => false,
                'disabled' => true,
                'label'    => 'daysetup.lastnewday',
            ])
            // Next new day
            ->add('nextnewday', TextType::class, [
                'required' => false,
                'disabled' => true,
                'label'    => 'daysetup.nextnewday',
            ])
            // Real time to offset new day
            ->add('gameoffsetseconds', NumberType::class, [
                'required'    => false,
                'empty_data'  => 0,
                'label'       => 'daysetup.gameoffsetseconds',
                'constraints' => [
                    new Assert\DivisibleBy(1),
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
