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

use Lotgd\Bundle\CompanionBundle\Admin\CompanionAdmin;
use Lotgd\Bundle\CompanionBundle\Entity\Companions;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\CompanionBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Resources/',
                '../../Tests/',
                '../../LotgdCompanionBundle.php',
            ])
        // ->load('Lotgd\Bundle\CompanionBundle\Controller\\', '../../Controller/')
        //     ->tag('controller.service_arguments')


        //-- Admin for companion
        ->set('lotgd_companion.admin', CompanionAdmin::class)
            ->args([null, Companions::class, null])
            ->call('setTranslationDomain', ['lotgd_companion_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.companion.group',
                'label' => 'menu.admin.companion.label_companion',
                'label_catalogue' => 'lotgd_companion_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'on_top' => true
            ])
            ->public()
    ;
};
