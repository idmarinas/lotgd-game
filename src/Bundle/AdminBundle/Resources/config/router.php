<?php

namespace Symfony\Component\Routing\Loader\Configurator;

return function (RoutingConfigurator $routes) {
    $routes->import('../../Controller/', 'annotation')->prefix('%lotgd_bundle.router.prefix.admin_panel%');
    $routes
        ->import('@SonataAdminBundle/Resources/config/routing/sonata_admin.xml')
            ->prefix('%lotgd_bundle.router.prefix.admin_panel%')
    ;
    $routes
        ->import('.', 'sonata_admin')
            ->prefix('%lotgd_bundle.router.prefix.admin_panel%')
    ;
    $routes
        ->import('@SonataTranslationBundle/Resources/config/routes.yaml')
            ->prefix('/change')
    ;
};
