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

return static function (ContainerConfigurator $container): void
{
    $container->extension('sonata_admin', [
        'title'      => 'LoTGD Admin',
        'title_logo' => '/bundles/lotgdui/images/Medallion-Green.gif',
        'dashboard'  => [
            'blocks' => [
                // Welcome message
                [
                    'type'     => 'sonata.block.service.text',
                    'position' => 'left',
                    'settings' => [
                        'template' => '@LotgdAdmin/block/dashboard_welcome.html.twig',
                    ],
                ],
                // RSS Feeds of LoTGD Core
                ['type' => 'lotgd_admin.block.service.dashboard.rss', 'position' => 'left'],
                // List of admins panels
                ['type' => 'sonata.admin.block.admin_list', 'position' => 'right'],
                // Total accounts
                [
                    'class'    => 'col-lg-2 col-xs-6',
                    'position' => 'top',
                    'type'     => 'sonata.admin.block.stats',
                    'settings' => [
                        'code'  => 'lotgd_user.admin',
                        'icon'  => 'fa-user', // font awesome icon
                        'text'  => 'lotgd_user.dashboard.top.total_accounts', // static text or translation message
                        'color' => 'bg-blue',
                    ],
                ],
                // Total unverified accounts
                [
                    'class'    => 'col-lg-2 col-xs-6',
                    'position' => 'top',
                    'type'     => 'sonata.admin.block.stats',
                    'settings' => [
                        'code'    => 'lotgd_user.admin',
                        'icon'    => 'fa-user', // font awesome icon
                        'text'    => 'lotgd_user.dashboard.top.total_unverified_accounts',
                        'color'   => 'bg-yellow',
                        'filters' => [
                            'isVerified' => ['value' => 2],
                        ],
                    ],
                ],
                // Total Companions
                [
                    'class'    => 'col-lg-2 col-xs-6',
                    'position' => 'top',
                    'type'     => 'sonata.admin.block.stats',
                    'settings' => [
                        'code'    => 'lotgd_companion.admin',
                        'icon'    => 'ion-ios-people', // font awesome icon
                        'text'    => 'lotgd_companion.dashboard.top.total_companions',
                        'color'   => 'bg-olive',
                    ],
                ],
                // Total creatures
                [
                    'class'    => 'col-lg-2 col-xs-6',
                    'position' => 'top',
                    'type'     => 'sonata.admin.block.stats',
                    'settings' => [
                        'code'    => 'lotgd_creature.admin',
                        'icon'    => 'fa-paw', // font awesome icon
                        'text'    => 'lotgd_creature.dashboard.top.total_creatures',
                        'color'   => 'bg-green',
                    ],
                ],
            ],
            'groups' => [
                'menu.admin.user.group' => [
                    'icon' => '<i class="fa fa-user"></i>',
                ],
                'menu.admin.action.group' => [
                    'icon' => '<i class="fa fa-cog"></i>',
                    'label_catalogue' => 'lotgd_admin_default',
                ],
                'menu.admin.editor.group' => [
                    'icon' => '<i class="fa fa-pencil"></i>',
                    'label_catalogue' => 'lotgd_admin_default',
                ],
                'menu.admin.npc.group' => [
                    'icon' => '<i class="fa fa-male"></i>',
                    'label_catalogue' => 'lotgd_admin_default',
                ],
                'menu.admin.logdnet.group' => [
                    'icon' => '<i class="fa fa-wifi"></i>',
                ],
                'menu.admin.settings.group' => [
                    'icon' => '<i class="fa fa-cogs"></i>',
                ],
            ],
        ],
        'security' => [
            'handler'          => 'sonata.admin.security.handler.role',
            'role_admin'       => 'ROLE_ADMIN',
            'role_super_admin' => 'ROLE_SUPER_ADMIN',
        ],
        'options' => [
            'lock_protection' => true,
        ],
        'templates' => [
            'user_block'             => '@LotgdAdmin/bundles/SonataAdmin/Core/user_block.html.twig',
            'layout'                 => '@LotgdAdmin/bundles/SonataAdmin/standard_layout.html.twig',
            'edit'                   => '@LotgdAdmin/bundles/SonataAdmin/CRUD/edit.html.twig',
            'outer_list_rows_mosaic' => '@LotgdAdmin/bundles/SonataAdmin/CRUD/list_outer_rows_mosaic.html.twig',
            'outer_list_rows_list'   => '@LotgdAdmin/bundles/SonataAdmin/CRUD/list_outer_rows_list.html.twig',
        ],
    ]);

    $container->extension('sonata_block', [
        'blocks' => [
            'sonata.admin.block.admin_list' => [
                'contexts' => ['admin'],
            ],
        ],
    ]);

    $container->extension('sonata_doctrine_orm_admin', [
        'templates' => [
            'types' => [
                'show' => [
                    'avatar' => '@LotgdAdmin/bundles/SonataAdmin/CRUD/show_setting_type_value.html.twig',
                ],
                'list' => [
                    'setting_type_value' => '@LotgdAdmin/bundles/SonataAdmin/CRUD/list_setting_type_value.html.twig',
                ],
            ],
        ],
    ]);
};
