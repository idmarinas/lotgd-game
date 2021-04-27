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

use Doctrine\ORM\EntityRepository;
use Laminas\Filter;
use Lotgd\Bundle\CoreBundle\Entity\Paylog;
use Lotgd\Bundle\CoreBundle\Form\Type\NumberType;
use Lotgd\Bundle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('txnid', EntityType::class, [
                'class'         => Paylog::class,
                'label'         => 'form.donator.txnid',
                'required'      => false,
                'query_builder' => function (EntityRepository $er)
                {
                    return $er->createQueryBuilder('u')
                        ->where('u.processed = 0') //-- Select all that not are processed
                    ;
                },
                'choice_label' => function ($paylog)
                {
                    return $paylog->getTxnid().') '.$paylog->getName();
                },
            ])
            ->add('user', EntityType::class, [
                'class'        => User::class,
                'label'        => 'form.donator.id',
                'choice_label' => 'username',
            ])
            ->add('points', NumberType::class, [
                'label'       => 'form.donator.points',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\NotEqualTo(0),
                    new Assert\DivisibleBy(1),
                ],
            ])
            ->add('reason', TextareaType::class, [
                'label'       => 'form.donator.reason',
                'required'    => false,
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 65000]),
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
