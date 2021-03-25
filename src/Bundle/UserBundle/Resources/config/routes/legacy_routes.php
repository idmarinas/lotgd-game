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

namespace Symfony\Component\Routing\Loader\Configurator;

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;

/**
 * Redirect old files to new routes.
 */
return function (RoutingConfigurator $routes)
{
    $routes
        // create.php?op=forgot
        ->add('lotgd_core_legacy_redirect_create_forgot', '/create.php')
            ->controller(RedirectController::class)
            ->condition("request.query.get('op') == 'forgot'")
            ->defaults([
                'route' => 'lotgd_user_forgot_password_request',
                'permanent' => true,
                'keepQueryParams' => false,
                'keepRequestMethod' => true
            ])
        ->add('lotgd_core_legacy_redirect_create', '/create.php')
            ->controller(RedirectController::class)
            ->defaults([
                'route' => 'lotgd_user_register',
                'permanent' => true,
                'keepQueryParams' => false,
                'keepRequestMethod' => true
            ])
    ;
};
