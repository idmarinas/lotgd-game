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

use Lotgd\Core\Form\ConfigurationType\GameSetupType;
use Lotgd\Core\Form\ConfigurationType\MaintenanceType;
use Lotgd\Core\Form\ConfigurationType\HomeType;
use Lotgd\Core\Form\ConfigurationType\AccountType;
use Lotgd\Core\Form\ConfigurationType\CommentaryType;
use Lotgd\Core\Form\ConfigurationType\PlacesType;
use Lotgd\Core\Form\ConfigurationType\SuTitleType;
use Lotgd\Core\Form\ConfigurationType\ReferralType;
use Lotgd\Core\Form\ConfigurationType\EventsType;
use Lotgd\Core\Form\ConfigurationType\DonationType;
use Lotgd\Core\Form\ConfigurationType\CombatType;
use Lotgd\Core\Form\ConfigurationType\TrainingType;
use Lotgd\Core\Form\ConfigurationType\ClansType;
use Lotgd\Core\Form\ConfigurationType\NewdaysType;
use Lotgd\Core\Form\ConfigurationType\ForestType;
use Lotgd\Core\Form\ConfigurationType\EnemiesType;
use Lotgd\Core\Form\ConfigurationType\CompanionType;
use Lotgd\Core\Form\ConfigurationType\BankType;
use Lotgd\Core\Form\ConfigurationType\MailType;
use Lotgd\Core\Form\ConfigurationType\PvpType;
use Lotgd\Core\Form\ConfigurationType\ContentType;
use Lotgd\Core\Form\ConfigurationType\LogdnetType;
use Lotgd\Core\Form\ConfigurationType\DaysetupType;
use Lotgd\Core\Form\ConfigurationType\MiscType;
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
        $builder->add('game_setup', GameSetupType::class, ['label' => 'game.setup.title']);
        $builder->add('maintenance', MaintenanceType::class, ['label' => 'maintenance.title']);
        $builder->add('home', HomeType::class, ['label' => 'home.title']);
        $builder->add('account', AccountType::class, ['label' => 'account.title']);
        $builder->add('commentary', CommentaryType::class, ['label' => 'commentary.title']);
        $builder->add('places', PlacesType::class, ['label' => 'places.title']);
        $builder->add('su_title', SuTitleType::class, ['label' => 'su.title.title']);
        $builder->add('referral', ReferralType::class, ['label' => 'referral.title']);
        $builder->add('events', EventsType::class, ['label' => 'events.title']);
        $builder->add('donation', DonationType::class, ['label' => 'donation.title']);
        $builder->add('combat', CombatType::class, ['label' => 'combat.title']);
        $builder->add('training', TrainingType::class, ['label' => 'training.title']);
        $builder->add('clans', ClansType::class, ['label' => 'clans.title']);
        $builder->add('newdays', NewdaysType::class, ['label' => 'newdays.title']);
        $builder->add('forest', ForestType::class, ['label' => 'forest.title']);
        $builder->add('enemies', EnemiesType::class, ['label' => 'enemies.title']);
        $builder->add('companion', CompanionType::class, ['label' => 'companion.title']);
        $builder->add('bank', BankType::class, ['label' => 'bank.title']);
        $builder->add('mail', MailType::class, ['label' => 'mail.title']);
        $builder->add('pvp', PvpType::class, ['label' => 'pvp.title']);
        $builder->add('content', ContentType::class, ['label' => 'content.title']);
        $builder->add('logdnet', LogdnetType::class, ['label' => 'logdnet.title']);
        $builder->add('daysetup', DaysetupType::class, ['label' => 'daysetup.title']);
        $builder->add('misc', MiscType::class, ['label' => 'misc.title']);

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
                $fieldsField = \array_filter($fieldsField, fn($val) => ! $val->isDisabled());

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
