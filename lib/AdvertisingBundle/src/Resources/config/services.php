<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.11.0
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Laminas\View\Helper\InlineScript;
use LotgdCore\AdvertisingBundle\Provider\AdsenseAdvertising;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->set('lotgd_core_advertising.adsense', AdsenseAdvertising::class)
            ->public()
            ->args([
                service(InlineScript::class),
                param('kernel.environment')
            ])
        ->alias(AdsenseAdvertising::class, 'lotgd_core_advertising.adsense')
    ;
};
