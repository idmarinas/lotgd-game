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

return function (RoutingConfigurator $routes)
{
    $routes->import('../../../Controller/RegistrationController.php', 'annotation')
        ->prefix('%lotgd_bundle.router.prefix.public%')
    ;
    $routes->import('../../../Controller/ResetPasswordController.php', 'annotation')
        ->prefix('%lotgd_bundle.router.prefix.public%')
    ;
    $routes->import('../../../Controller/ProfileController.php', 'annotation')
        ->prefix('%lotgd_bundle.router.prefix.authenticated%')
    ;
};
