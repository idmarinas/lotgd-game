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

// use Lotgd\Bundle\CoreBundle\Menu\MenuBuilder;

return static function (ContainerConfigurator $container)
{
    $container->services()
        // ->defaults()
        //     ->autowire()
        //     ->autoconfigure()

        // ->load('Lotgd\Bundle\UiBundle\\', '../../*')
        //     ->exclude([
        //         '../../DependencyInjection/',
        //         '../../Entity/',
        //         '../../Resources/',
        //         '../../Tests/',
        //         '../../LotgdUiBundle.php',
        //     ])
        // ->load('Lotgd\Bundle\UiBundle\Controller\\', '../../Controller/')
        //     ->tag('controller.service_arguments')

        //-- Menu
        // ->set('lotgd.menu_builder', MenuBuilder::class)
        //     ->args([
        //         service('knp_menu.factory'),
        //         service('event_dispatcher')
        //     ])
        //     ->tag('knp_menu.menu_builder', [
        //         'method' => 'createMenuCore',
        //         'alias' => 'lotgd.menu_core' //-- The alias is what is used to retrieve the menu
        //     ])
    ;
};
