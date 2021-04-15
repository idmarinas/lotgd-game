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

use Lotgd\Bundle\MountBundle\Admin\MountAdmin;
use Lotgd\Bundle\MountBundle\Entity\Mounts;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\MountBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Resources/',
                '../../Tests/',
                '../../LotgdMountBundle.php',
            ])
        // ->load('Lotgd\Bundle\MountBundle\Controller\\', '../../Controller/')
        //     ->tag('controller.service_arguments')


        //-- Admin for mount
        ->set('lotgd_mount.admin', MountAdmin::class)
            ->args([null, Mounts::class, null])
            ->call('setTranslationDomain', ['lotgd_mount_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.npc.group',
                'label' => 'menu.admin.mount.label_mount',
                'label_catalogue' => 'lotgd_mount_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
            ])
            ->public()
    ;
};
