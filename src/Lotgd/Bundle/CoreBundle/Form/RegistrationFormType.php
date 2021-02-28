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

use Lotgd\Bundle\CoreBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, ['label' => 'username'])
            ->add('email', EmailType::class, ['label' => 'email'])
            ->add('plainPassword', RepeatedType::class, [
                'mapped'          => false,
                'type'            => PasswordType::class,
                'invalid_message' => 'password.not.match',
                'required'        => true,
                'first_options'   => ['label' => 'plain_password'],
                'second_options'  => ['label' => 'confirmation_password'],
                'constraints'     => [
                    new Constraints\NotBlank(),
                    new Constraints\NotNull(),
                    new Constraints\NotCompromisedPassword(),
                    new Constraints\Length([
                        'min' => 6,
                        // 'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'       => 'agree_terms',
                'mapped'      => false,
                'constraints' => [
                    new Constraints\IsTrue(),
                ],
            ])
            ->add('agreePrivacy', CheckboxType::class, [
                'label'       => 'agree_privacy',
                'mapped'      => false,
                'constraints' => [
                    new Constraints\IsTrue(),
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'submit',
                'attr'  => [
                    'class' => 'center aligned big primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => User::class,
            'translation_domain' => 'lotgd_core_form_registration',
        ]);
    }
}
