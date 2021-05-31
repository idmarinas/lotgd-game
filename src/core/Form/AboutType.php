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

namespace Lotgd\Core\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AboutType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Game Setup
            ->add('game_setup', AboutType\GameSetupType::class, ['label' => 'game.setup.title'])
            // New Days
            ->add('newday', AboutType\NewdayType::class, ['label' => 'newday.title'])
            // Bank settings
            ->add('bank', AboutType\BankType::class, ['label' => 'bank.title'])
            // Forest
            ->add('forest', AboutType\ForestType::class, ['label' => 'forest.title'])
            // Mail Settings
            ->add('mail', AboutType\MailType::class, ['label' => 'mail.title'])
            // Content Expiration
            ->add('content', AboutType\ContentType::class, ['label' => 'content.title'])
            // Useful Information
            ->add('info', AboutType\MiscType::class, ['label' => 'info.title'])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'label'              => 'form.label',
            'translation_domain' => 'form_core_about',
        ]);
    }
}
