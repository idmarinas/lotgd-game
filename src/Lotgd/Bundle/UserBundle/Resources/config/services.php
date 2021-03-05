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

use Lotgd\Bundle\UserBundle\Admin\UserAdmin;
use Lotgd\Bundle\UserBundle\Entity\User;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\UserBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Resources/',
                '../../Tests/',
                '../../LotgdUserBundle.php',
            ])
        ->load('Lotgd\Bundle\UserBundle\Controller\\', '../../Controller/')
            ->tag('controller.service_arguments')


        //-- Admin for user
        ->set('lotgd_user.admin', UserAdmin::class)
            ->args([null, User::class, null])
            ->call('setTranslationDomain', ['lotgd_user_admin'])
            ->tag('sonata.admin', [
                'manager_type' => 'orm',
                'group' => 'menu.admin.user.group',
                'icon' => '<i class="fa fa-user"></i>',
                'label' => 'menu.admin.user.label_user',
                'label_catalogue' => 'lotgd_user_admin',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'on_top' => true
            ])
            ->public()
    ;
};
