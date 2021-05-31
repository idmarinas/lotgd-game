<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\EntityForm;

use Lotgd\Core\Entity\Cronjob;
use Lotgd\Core\Form\Type\CronjobListType as TypeCronjobType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronjobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'name'])
            ->add('schedule', TextType::class, ['label' => 'schedule'])
            ->add('command', TypeCronjobType::class, ['label' => 'command', 'help' => 'command.note'])
            ->add('runAs', TextType::class, ['label' => 'run_as', 'required' => false])
            ->add('debug', CheckboxType::class, ['label' => 'debug', 'required' => false])
            ->add('environment', TextareaType::class, ['label' => 'environment', 'required' => false])
            ->add('runOnHost', TextType::class, ['label' => 'run_on_host', 'required' => false])
            ->add('maxRuntime', NumberType::class, ['label' => 'max_runtime', 'required' => false])
            ->add('enabled', CheckboxType::class, ['label' => 'enabled', 'required' => false])
            ->add('haltDir', TextType::class, ['label' => 'halt_dir', 'required' => false])
            ->add('output', TextType::class, ['label' => 'output', 'required' => false])
            ->add('dateFormat', TextType::class, ['label' => 'date_format', 'required' => false])
            // ->add('recipients', TextType::class, ['label' => 'recipients', 'required' => false])
            ->add('mailer', TextType::class, ['label' => 'mailer', 'required' => false])
            ->add('smtpHost', TextType::class, ['label' => 'smtp_host', 'required' => false])
            ->add('smtpPort', NumberType::class, ['label' => 'smtp_port', 'required' => false])
            ->add('smtpUsername', TextType::class, ['label' => 'smtp_username', 'required' => false])
            ->add('smtpPassword', TextType::class, ['label' => 'smtp_password', 'required' => false])
            ->add('smtpSecurity', TextType::class, ['label' => 'smtp_security', 'required' => false])
            ->add('smtpSender', TextType::class, ['label' => 'smtp_sender', 'required' => false])
            ->add('smtpSenderName', TextType::class, ['label' => 'smtp_sender_name', 'required' => false])

            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Cronjob::class,
            'translation_domain' => 'form_core_cronjob',
        ]);
    }
}
