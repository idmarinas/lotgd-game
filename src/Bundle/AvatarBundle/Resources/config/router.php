<?php

namespace Symfony\Component\Routing\Loader\Configurator;

return function (RoutingConfigurator $routes) {
    $routes->import('../../Controller/', 'annotation')->prefix('%lotgd_bundle.router.prefix.authenticated%');
};
