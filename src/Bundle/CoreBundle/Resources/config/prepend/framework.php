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

return static function (ContainerConfigurator $container): void
{
    $container->extension('framework', [
        'csrf_protection' => true,
        'session'         => [
            'enabled'         => true,
            'name'            => 'LegendOfTheGreenDragon',
            'handler_id'      => null,
            'cookie_secure'   => true,
            'cookie_samesite' => 'lax',
            'use_cookies'     => true,
        ],
        'http_client' => [
            'default_options' => [
                'headers' => [
                    'User-Agent' => 'LoTGD Core Package/RSS Reader'
                ]
            ]
                ],
        'validation' => [
            'auto_mapping' => [
                'Lotgd\\CoreBundle\\Entity\\' => []
            ]
        ]
    ]);
};
