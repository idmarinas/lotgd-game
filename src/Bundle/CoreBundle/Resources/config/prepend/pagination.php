<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void
{
    $container->extension('knp_paginator', [
        'page_range' => 5,
        'template' => [
            'pagination' => '@KnpPaginator/Pagination/semantic_ui_pagination.html.twig'
        ]
    ]);
};
