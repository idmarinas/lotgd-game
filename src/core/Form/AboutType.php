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

use Lotgd\Core\Form\AboutType\GameSetupType;
use Lotgd\Core\Form\AboutType\NewdayType;
use Lotgd\Core\Form\AboutType\BankType;
use Lotgd\Core\Form\AboutType\ForestType;
use Lotgd\Core\Form\AboutType\MailType;
use Lotgd\Core\Form\AboutType\ContentType;
use Lotgd\Core\Form\AboutType\MiscType;
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
            ->add('game_setup', GameSetupType::class, ['label' => 'game.setup.title'])
            // New Days
            ->add('newday', NewdayType::class, ['label' => 'newday.title'])
            // Bank settings
            ->add('bank', BankType::class, ['label' => 'bank.title'])
            // Forest
            ->add('forest', ForestType::class, ['label' => 'forest.title'])
            // Mail Settings
            ->add('mail', MailType::class, ['label' => 'mail.title'])
            // Content Expiration
            ->add('content', ContentType::class, ['label' => 'content.title'])
            // Useful Information
            ->add('info', MiscType::class, ['label' => 'info.title'])
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
