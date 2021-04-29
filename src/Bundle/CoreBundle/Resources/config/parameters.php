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

return static function (ContainerConfigurator $container)
{
    //-- Register parameters
    $container->parameters()
        ->set('lotgd_bundle.logdnet.central_server', 'http://lotgd.net')
        ->set('lotgd_bundle.seo.title.default', 'Legend of the Green Dragon')
        ->set('lotgd_bundle.number.format.decimal.point', '.')
        ->set('lotgd_bundle.number.format.thousands.sep', ',')
        ->set('lotgd_bundle.router.prefix.public', '/game')
        ->set('lotgd_bundle.router.prefix.authenticated', '/play')
        ->set('lotgd_bundle.router.prefix.admin_panel', '/_grotto')
        ->set('lotgd_bundle.game.server.admin.email', '%env(GAME_ADMIN_EMAIL)%')
        ->set('lotgd_bundle.game.server.admin.mailed_petitions', false)
        ->set('lotgd_bundle.paypal.site.currency', 'USD')
        ->set('lotgd_bundle.paypal.site.country', 'US')
        ->set('lotgd_bundle.paypal.site.email', '')
        ->set('lotgd_bundle.donation.points_per_currency_unit', 100)
    ;
};
