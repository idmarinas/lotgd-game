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

return static function (ContainerConfigurator $container): void {
    $container->extension('security', [
        'providers' => [
            'lotgd_admin_user_provider' => [
                'id' => 'Lotgd\Bundle\AdminBundle\Security\UserProvider',
            ]
        ]
    ]);
};
