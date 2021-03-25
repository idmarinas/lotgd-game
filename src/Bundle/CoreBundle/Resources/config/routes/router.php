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

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;

return function (RoutingConfigurator $routes)
{
    $routes->import('../../../Controller/', 'annotation');

    // About pages
    $routes
        ->add('lotgd_core_about', '/about')
            ->controller(TemplateController::class)
            ->defaults([
                // the path of the template to render
                'template' => '@LotgdCore/about/index.html.twig',
                // special options defined by Symfony to set the page cache
                'maxAge' => 86400,
                'sharedAge' => 86400
            ])

        ->add('lotgd_core_about_license', '/about/license')
            ->controller(TemplateController::class)
            ->defaults([
                'template' => '@LotgdCore/about/license.html.twig',
                'maxAge' => 86400,
                'sharedAge' => 86400
            ])
    ;
};
