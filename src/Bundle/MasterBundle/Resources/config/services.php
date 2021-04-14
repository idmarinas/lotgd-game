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

use Lotgd\Bundle\MasterBundle\Admin\MasterAdmin;
use Lotgd\Bundle\MasterBundle\Entity\Masters;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\MasterBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Resources/',
                '../../Tests/',
                '../../LotgdMasterBundle.php',
            ])
        // ->load('Lotgd\Bundle\MasterBundle\Controller\\', '../../Controller/')
        //     ->tag('controller.service_arguments')

        //-- Admin for master
        ->set('lotgd_master.admin', MasterAdmin::class)
            ->args([null, Masters::class, null])
            ->call('setTranslationDomain', ['lotgd_master_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.npc.group',
                'label' => 'menu.admin.master.label_master',
                'label_catalogue' => 'lotgd_master_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
            ])
            ->public()
    ;
};
