<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Form;

use Laminas\Filter;
use Lotgd\Bundle\CoreBundle\Form\Type\NumberType;
use Lotgd\Bundle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DonationManualType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('txnid', TextType::class, [
                'label'       => 'form.donator.txnid',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 65000]),
                ],
                'filters' => [
                    new Filter\StripTags(),
                ],
            ])
            ->add('user', EntityType::class, [
                'class'        => User::class,
                'label'        => 'form.donator.id',
                'choice_label' => 'username',
            ])
            ->add('amount', NumberType::class, [
                'label'       => 'form.donator.amount',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\GreaterThan(0)
                ],
            ])
            ->add('reason', TextareaType::class, [
                'label'       => 'form.donator.reason',
                'constraints' => [
                    new Assert\Length(['max' => 65000]),
                    new Assert\NotNull(),
                ],
                'filters' => [
                    new Filter\StripTags(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'lotgd_core_admin',
        ]);
    }
}
