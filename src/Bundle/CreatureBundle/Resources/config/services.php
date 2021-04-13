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

use Lotgd\Bundle\CreatureBundle\Admin\CreatureAdmin;
use Lotgd\Bundle\CreatureBundle\Entity\Creatures;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\CreatureBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Resources/',
                '../../Tests/',
                '../../LotgdCreatureBundle.php',
            ])
        // ->load('Lotgd\Bundle\CreatureBundle\Controller\\', '../../Controller/')
        //     ->tag('controller.service_arguments')


        //-- Admin for creature
        ->set('lotgd_creature.admin', CreatureAdmin::class)
            ->args([null, Creatures::class, null])
            ->call('setTranslationDomain', ['lotgd_creature_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.creature.group',
                'label' => 'menu.admin.creature.label_creature',
                'label_catalogue' => 'lotgd_creature_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'on_top' => true
            ])
            ->public()
    ;
};
