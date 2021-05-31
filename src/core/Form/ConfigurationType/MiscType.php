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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MiscType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Cost to resurrect from the dead?
            ->add('resurrectioncost', NumberType::class, [
                'required'    => false,
                'label'       => 'misc.resurrectioncost',
                'empty_data'  => 100,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // The Barkeeper may help you to switch your specialty?
            ->add('allowspecialswitch', CheckboxType::class, [
                'required' => false,
                'label'    => 'misc.allowspecialswitch',
            ])
            // Maximum number of items to be shown in the warrior list
            ->add('maxlistsize', NumberType::class, [
                'required'    => false,
                'label'       => 'misc.maxlistsize',
                'empty_data'  => 25,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\Positive(),
                ],
            ])
            // Does Merick have feed onhand for creatures
            ->add('allowfeed', CheckboxType::class, [
                'required' => false,
                'label'    => 'misc.allowfeed',
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
