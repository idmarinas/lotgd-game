<?php

namespace Symfony\Component\Routing\Loader\Configurator;

return function (RoutingConfigurator $routes) {
    $routes
        ->import('@FOSCommentBundle/Resources/config/routing.yml', 'rest')
            ->prefix('%lotgd_bundle.router.prefix.authenticated%/api/commentary')
            ->defaults(['_format' => 'html'])
    ;
};
