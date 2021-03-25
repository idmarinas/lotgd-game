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
        'encoders' => [
            'lotgd_old_encoder' => [
                'id' => 'Lotgd\Bundle\UserBundle\Security\LotgdPasswordEncoder'
            ],
            'Lotgd\Bundle\UserBundle\Entity\User' => [
                'algorithm' => 'auto',
                'migrate_from' => ['lotgd_old_encoder']
            ]
            ],
            'providers' => [
                'lotgd_core_user_provider' => [
                    'entity' => [
                        'class' => 'Lotgd\Bundle\UserBundle\Entity\User',
                        'property' => 'username'
                    ]
                ]
            ]
    ]);
};
