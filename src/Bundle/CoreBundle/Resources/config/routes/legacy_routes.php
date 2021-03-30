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

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;

/**
 * Redirect old files to new routes.
 */
return function (RoutingConfigurator $routes)
{
    $routes
        // home.php
        ->add('lotgd_core_legacy_redirect_home', '/home.php')
            ->controller(RedirectController::class)
            ->defaults([
                'route' => 'lotgd_core_home',
                // optionally you can define some arguments passed to the route
                // page: 'index'
                // version: 'current'
                // redirections are temporary by default (code 302) but you can make them permanent (code 301)
                'permanent' => true,
                // add this to keep the original query string parameters when redirecting
                'keepQueryParams' => false,
                // add this to keep the HTTP method when redirecting. The redirect status changes
                // * for temporary redirects, it uses the 307 status code instead of 302
                // * for permanent redirects, it uses the 308 status code instead of 301
                'keepRequestMethod' => true
            ])
        // about.php?op=license
        ->add('lotgd_core_legacy_redirect_about_license', '/about.php')
            ->controller(RedirectController::class)
            ->condition("request.query.get('op') == 'license'")
            ->defaults([
                'route' => 'lotgd_core_about_license',
                'permanent' => true,
                'keepQueryParams' => false,
                'keepRequestMethod' => true
            ])
        // about.php?op=listmodules
        ->add('lotgd_core_legacy_redirect_about_module', '/about.php')
            ->controller(RedirectController::class)
            ->condition("request.query.get('op') == 'listmodules'")
            ->defaults([
                'route' => 'lotgd_core_about_bundles',
                'permanent' => true,
                'keepQueryParams' => false,
                'keepRequestMethod' => true
            ])
        // about.php
        ->add('lotgd_core_legacy_redirect_about', '/about.php')
            ->controller(RedirectController::class)
            ->defaults([
                'route' => 'lotgd_core_about',
                'permanent' => true,
                'keepQueryParams' => false,
                'keepRequestMethod' => true
            ])
        // images/logdnet.php
        ->add('lotgd_core_page_logdnet_redirect_image', '/images/logdnet.php')
            ->controller(RedirectController::class)
            ->defaults([
                'route' => 'lotgd_core_page_logdnet_image',
                'permanent' => true,
                'keepQueryParams' => true,
                'keepRequestMethod' => true
            ])
        // logdnet.php?op=net
        ->add('lotgd_core_page_logdnet_redirect_net', '/logdnet.php')
            ->condition("request.query.get('op') == 'net'")
            ->controller(RedirectController::class)
            ->defaults([
                'route' => 'lotgd_core_page_logdnet_net',
                'permanent' => true,
                'keepQueryParams' => false,
                'keepRequestMethod' => true
            ])
        // logdnet.php?op=list
        ->add('lotgd_core_page_logdnet_redirect_list', '/logdnet.php')
            ->condition("request.query.get('op') == 'list'")
            ->controller(RedirectController::class)
            ->defaults([
                'route' => 'lotgd_core_page_logdnet_list',
                'permanent' => true,
                'keepQueryParams' => false,
                'keepRequestMethod' => true
            ])
        // logdnet.php
        ->add('lotgd_core_page_logdnet_redirect_register', '/logdnet.php')
            ->controller(RedirectController::class)
            ->defaults([
                'route' => 'lotgd_core_page_logdnet_register',
                'permanent' => true,
                'keepQueryParams' => true,
                'keepRequestMethod' => true
            ])
    ;
};
