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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReferralType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('refereraward', NumberType::class, [
                'required'    => false,
                'label'       => 'referral.refereraward',
                'empty_data'  => 25,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('referminlevel', NumberType::class, [
                'required'    => false,
                'label'       => 'referral.referminlevel',
                'empty_data'  => 10,
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
            'translation_domain' => 'form-core-grotto-configuration',
        ]);
    }
}
