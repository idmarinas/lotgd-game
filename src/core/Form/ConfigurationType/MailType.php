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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Message size limit per message
            ->add('mailsizelimit', NumberType::class, [
                'required'    => false,
                'label'       => 'mail.mailsizelimit',
                'empty_data'  => 1024,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\Positive(),
                ],
            ])
            // Limit # of messages in inbox
            ->add('inboxlimit', NumberType::class, [
                'required'    => false,
                'label'       => 'mail.inboxlimit',
                'empty_data'  => 50,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\Positive(),
                ],
            ])
            // Automatically delete old messages after (days)
            ->add('oldmail', NumberType::class, [
                'required'    => false,
                'label'       => 'mail.oldmail',
                'empty_data'  => 50,
                'constraints' => [
                    new Assert\DivisibleBy(1),
                    new Assert\PositiveOrZero(),
                ],
            ])
            // Warning to give when attempting to YoM an admin?
            ->add('superuseryommessage', TextareaType::class, [
                'required'    => false,
                'label'       => 'mail.superuseryommessage',
                'constraints' => [
                    new Assert\Length(['min' => 3, 'max' => 255]),
                ],
            ])
            // Only unread mail count towards the inbox limit?
            ->add('onlyunreadmails', CheckboxType::class, [
                'required' => false,
                'label'    => 'mail.onlyunreadmails',
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
