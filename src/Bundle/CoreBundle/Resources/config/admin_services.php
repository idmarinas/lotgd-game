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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Lotgd\Bundle\CoreBundle\Admin;
use Lotgd\Bundle\CoreBundle\Controller;
use Lotgd\Bundle\CoreBundle\Entity;
use Lotgd\Bundle\UserBundle\Entity as UserEntity;

return static function (ContainerConfigurator $container)
{
    $container->services()
        //-- Admin for Logdnet
        ->set('lotgd_logdnet.admin', Admin\LogdnetAdmin::class)
            ->args([null, Entity\Logdnet::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.logdnet.group',
                'label' => 'menu.admin.logdnet.label_logdnet',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'on_top' => true
            ])
            ->public()

        //-- Admin for titles
        ->set('lotgd_title.admin', Admin\TitleAdmin::class)
            ->args([null, Entity\Titles::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.editor.group',
                'label' => 'menu.admin.title.label_title',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
        //-- Admin for armor
        ->set('lotgd_armor.admin', Admin\ArmorAdmin::class)
            ->args([null, Entity\Armor::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.editor.group',
                'label' => 'menu.admin.armor.label_armor',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
        //-- Admin for weapon
        ->set('lotgd_weapon.admin', Admin\WeaponAdmin::class)
            ->args([null, Entity\Weapons::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.editor.group',
                'label' => 'menu.admin.weapon.label_weapon',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
        //-- Admin for paylog
        ->set('lotgd_paylog.admin', Admin\PaylogAdmin::class)
            ->args([null, Entity\Paylog::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.action.group',
                'label' => 'menu.admin.paylog.label_paylog',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
        //-- Admin for donator
        ->set('lotgd_donator.admin', Admin\DonatorAdmin::class)
            ->args([null, UserEntity\User::class, Controller\DonatorAdminController::class])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.action.group',
                'label' => 'menu.admin.donator.label_donator',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
        //-- Admin for Referers (url)
        ->set('lotgd_referer.admin', Admin\ReferersAdmin::class)
            ->args([null, Entity\Referers::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.mechanics.group',
                'label' => 'menu.admin.referer.label_referer',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
        //-- Admin for petitions
        ->set('lotgd_petition.admin', Admin\PetitionAdmin::class)
            ->args([null, Entity\Petition::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.action.group',
                'label' => 'menu.admin.petition.label_petition',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
        //-- Admin for petitions types (create/update/delete types of petitions)
        ->set('lotgd_petition_type.admin', Admin\PetitionTypeAdmin::class)
            ->args([null, Entity\PetitionType::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.configuration.group',
                'label' => 'menu.admin.petition_type.label_petition_type',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
        //-- Admin for alters users
        ->set('lotgd_alt_user.admin', Admin\AltUserAdmin::class)
            ->args([null, UserEntity\User::class, null])
            ->call('setTranslationDomain', ['lotgd_core_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.action.group',
                'label' => 'menu.admin.alt_user.label_alt_user',
                'label_catalogue' => 'lotgd_core_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
            ])
            ->public()
    ;
};
