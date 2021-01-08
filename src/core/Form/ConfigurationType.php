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

namespace Lotgd\Core\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('game_setup', ConfigurationType\GameSetupType::class, ['label' => 'game.setup.title']);
        $builder->add('maintenance', ConfigurationType\MaintenanceType::class, ['label' => 'maintenance.title']);
        $builder->add('home', ConfigurationType\HomeType::class, ['label' => 'home.title']);
        $builder->add('account', ConfigurationType\AccountType::class, ['label' => 'account.title']);
        $builder->add('commentary', ConfigurationType\CommentaryType::class, ['label' => 'commentary.title']);
        $builder->add('places', ConfigurationType\PlacesType::class, ['label' => 'places.title']);
        $builder->add('su_title', ConfigurationType\SuTitleType::class, ['label' => 'su.title.title']);
        $builder->add('referral', ConfigurationType\ReferralType::class, ['label' => 'referral.title']);
        $builder->add('events', ConfigurationType\EventsType::class, ['label' => 'events.title']);
        $builder->add('donation', ConfigurationType\DonationType::class, ['label' => 'donation.title']);
        $builder->add('combat', ConfigurationType\CombatType::class, ['label' => 'combat.title']);
        $builder->add('training', ConfigurationType\TrainingType::class, ['label' => 'training.title']);
        $builder->add('clans', ConfigurationType\ClansType::class, ['label' => 'clans.title']);
        $builder->add('newdays', ConfigurationType\NewdaysType::class, ['label' => 'newdays.title']);
        $builder->add('forest', ConfigurationType\ForestType::class, ['label' => 'forest.title']);
        $builder->add('enemies', ConfigurationType\EnemiesType::class, ['label' => 'enemies.title']);
        $builder->add('companion', ConfigurationType\CompanionType::class, ['label' => 'companion.title']);
        $builder->add('bank', ConfigurationType\BankType::class, ['label' => 'bank.title']);
        $builder->add('mail', ConfigurationType\MailType::class, ['label' => 'mail.title']);
        $builder->add('pvp', ConfigurationType\PvpType::class, ['label' => 'pvp.title']);
        $builder->add('content', ConfigurationType\ContentType::class, ['label' => 'content.title']);
        $builder->add('logdnet', ConfigurationType\LogdnetType::class, ['label' => 'logdnet.title']);
        $builder->add('daysetup', ConfigurationType\DaysetupType::class, ['label' => 'daysetup.title']);
        $builder->add('misc', ConfigurationType\MiscType::class, ['label' => 'misc.title']);

        $builder->add('submit', SubmitType::class, ['label' => 'button.save', 'translation_domain' => 'form_app']);

        //-- Listener to delete data duplicated in each field
        //-- Each field have all data settings, but not need all of this data
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event)
        {
            $fields = $event->getForm()->all();
            $data = $event->getData();

            foreach ($fields as $name => $field)
            {
                $fieldsField = $field->all();

                if ( ! isset($data[$name]))
                {
                    continue;
                }

                //-- Deleted fields that are disabled
                $fieldsField = \array_filter($fieldsField, function ($val)
                {
                    return ! $val->isDisabled();
                });

                $data[$name] = \array_intersect_key($data[$name], $fieldsField);
            }

            $event->setData($data);
        });

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'label'              => 'form.label',
            'translation_domain' => 'form_core_configuration',
        ]);
    }
}
