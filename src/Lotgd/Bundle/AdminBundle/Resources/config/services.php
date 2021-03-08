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

use Lotgd\Bundle\AdminBundle\Block\DashboardRssServiceBlock;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\AdminBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Migrations/',
                '../../Resources/',
                '../../Tests/',
                '../../LotgdAdminBundle.php',
            ])
        ->load('Lotgd\Bundle\AdminBundle\Controller\\', '../../Controller/')
            ->tag('controller.service_arguments')

        //-- Blocks
        ->set('lotgd_admin.block.service.dashboard.rss', DashboardRssServiceBlock::class)
            ->args([service('twig'), service('http_client')])
            ->tag('sonata.block')
    ;
};
