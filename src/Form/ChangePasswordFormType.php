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

namespace Lotgd\Core\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'mapped'          => false,
                'type'            => PasswordType::class,
                'invalid_message' => 'password.not.match',
                'required'        => true,
                'first_options'   => [
                    'label'       => 'change.plain_password',
                    'constraints' => [
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
                ],
                'second_options' => ['label' => 'change.confirmation_password'],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'change.submit',
                'attr'  => [
                    'class' => 'center aligned big primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'form_reset_password'
        ]);
    }
}
