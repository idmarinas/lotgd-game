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

use Lotgd\Bundle\CoreBundle\EventSubscriber\SettingsSubscriber;
use Lotgd\Bundle\SettingsBundle\Admin\SettingAdmin;
use Lotgd\Bundle\SettingsBundle\Admin\SettingDomainAdmin;
use Lotgd\Bundle\SettingsBundle\Entity\Setting;
use Lotgd\Bundle\SettingsBundle\Entity\SettingDomain;
use Lotgd\Bundle\SettingsBundle\EntityListener\SettingListener;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\SettingsBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Resources/',
                '../../Tests/',
                '../../LotgdSettingsBundle.php',
                '../../LotgdSettingsCache.php',
            ])

        //-- Admin panels
        ->set('lotgd_settings.admin', SettingAdmin::class)
            ->args([null, Setting::class, null])
            ->call('setTranslationDomain', ['lotgd_settings_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.settings.group',
                'label' => 'menu.admin.settings.label_settings',
                'label_catalogue' => 'lotgd_settings_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
            ])
            ->public()
        ->set('lotgd_settings.domain.admin', SettingDomainAdmin::class)
            ->args([null, SettingDomain::class, null])
            ->call('setTranslationDomain', ['lotgd_settings_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.settings.group',
                'label' => 'menu.admin.settings.label_settings_domain',
                'label_catalogue' => 'lotgd_settings_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
            ])
            ->public()

        // Entities Listeners
        ->set(SettingListener::class)
            ->tag('doctrine.orm.entity_listener', [ 'event' => 'preUpdate', 'entity' => Setting::class, 'lazy' => true ])
            ->tag('doctrine.orm.entity_listener', [ 'event' => 'postUpdate', 'entity' => Setting::class, 'lazy' => true ])
            ->tag('doctrine.orm.entity_listener', [ 'event' => 'prePersist', 'entity' => Setting::class, 'lazy' => true ])
            ->tag('doctrine.orm.entity_listener', [ 'event' => 'postPersist', 'entity' => Setting::class, 'lazy' => true ])
            ->tag('doctrine.orm.entity_listener', [ 'event' => 'preRemove', 'entity' => Setting::class, 'lazy' => true ])
            ->tag('doctrine.orm.entity_listener', [ 'event' => 'postRemove', 'entity' => Setting::class, 'lazy' => true ])

        ->set(SettingsSubscriber::class)
            ->args([service('lotgd.core.package.cache')])
            ->tag('kernel.event_subscriber')
    ;
};
