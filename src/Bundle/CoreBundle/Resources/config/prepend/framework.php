<?php

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
        ]
    ]);
};
