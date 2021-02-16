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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DivisibleBy;
use Symfony\Component\Validator\Constraints\Length;

class TrainingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Masters hunt down truant students
            ->add('automaster', CheckboxType::class, [
                'required' => false,
                'label'    => 'training.automaster',
            ])
            // Can players gain multiple levels (challenge multiple masters) per game day?
            ->add('multimaster', CheckboxType::class, [
                'required' => false,
                'label'    => 'training.multimaster',
            ])
            // Display news if somebody fought his master?
            ->add('displaymasternews', CheckboxType::class, [
                'required' => false,
                'label'    => 'training.displaymasternews.label',
                'help'     => 'training.displaymasternews.note',
            ])
            // Which is the maximum attainable level (at which also the Dragon shows up)?
            ->add('maxlevel', NumberType::class, [
                'label'       => 'training.maxlevel',
                'constraints' => [
                    new DivisibleBy(1),
                ],
            ])
            // Give here what experience is necessary for each level
            ->add('exp-array', TextType::class, [
                'label'       => 'training.exp.array.label',
                'help'        => 'training.exp.array.note',
                'constraints' => [
                    new Length(['min' => 0, 'max' => 255]),
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
