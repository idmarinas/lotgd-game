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

use Laminas\View\Helper\HeadLink;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadScript;
use Laminas\View\Helper\HeadStyle;
use Laminas\View\Helper\HeadTitle;
use Laminas\View\Helper\InlineScript;
use Laminas\View\Renderer\PhpRenderer;
use Lotgd\Bundle\CoreBundle\Controller\AboutController;
use Lotgd\Bundle\CoreBundle\Installer\Install;
use Lotgd\Bundle\CoreBundle\Menu\MenuBuilder;
use Lotgd\Bundle\CoreBundle\Tool\Censor;
use Lotgd\Bundle\CoreBundle\Tool\Code;
use Lotgd\Bundle\CoreBundle\Tool\Color;
use Lotgd\Bundle\CoreBundle\Tool\Format;
use Marek\Twig\ByteUnitsExtension;

return static function (ContainerConfigurator $container)
{
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()

        ->load('Lotgd\Bundle\CoreBundle\\', '../../*')
            ->exclude([
                '../../DependencyInjection/',
                '../../Entity/',
                '../../Migrations/',
                '../../Resources/',
                '../../Tests/',
                '../../Twig/Node/',
                '../../Twig/NodeVisitor/',
                '../../Twig/TokenParser/',
                '../../LotgdCoreBundle.php',
            ])
        ->load('Lotgd\Bundle\CoreBundle\Controller\\', '../../Controller/')
            ->tag('controller.service_arguments')

        ->set(AboutController::class)
            ->call('setBundles', [expr("service('kernel').getBundles()")])

        //-- Register install commands
        ->load('Lotgd\Bundle\CoreBundle\Installer\Command\\', '../../Installer/Command/')
            ->tag('console.command')

        //-- Install service
        ->set(Install::class)
            ->lazy()
            ->private()

        //-- View Laminas service
        ->set(HeadLink::class)
        ->set(HeadMeta::class)
            ->call('setView', [service(PhpRenderer::class)])
        ->set(HeadScript::class)
        ->set(HeadStyle::class)
        ->set(HeadTitle::class)
        ->set(InlineScript::class)
        ->set(PhpRenderer::class)

        //-- Tools
        ->set('lotgd_core.format', Format::class)
            ->args([
                service('translator'),
                service(Code::class),
                service(Color::class)
            ])
            ->call('setDecPoint', [param('lotgd_core.number.format.decimal.point')])
            ->call('setThousandsSep', [param('lotgd_core.number.format.thousands.sep')])
            ->public()
        ->alias(Format::class, 'lotgd_core.format')

        ->set('lotgd_core.censor', Censor::class)
            ->lazy()
            ->args([
                param('kernel.default_locale'),
                param('kernel.project_dir')
            ])
            ->public()
        ->alias(Censor::class, 'lotgd_core.censor')

#     # Lotgd\Core\Tool\Sanitize:
#     #     public: true

        //-- Twig extensions
        ->set(ByteUnitsExtension::class)
            ->tag('twig.extension')
            ->lazy()

#     Lotgd\Core\Twig\Extension\JaxonExtension:
#         lazy: true

        //-- Menu
        ->set('lotgd_core.menu_builder', MenuBuilder::class)
            ->args([
                service('knp_menu.factory'),
                service('event_dispatcher')
            ])
            ->tag('knp_menu.menu_builder', [
                'method' => 'createMenuCore',
                'alias' => 'lotgd_core.menu' //-- The alias is what is used to retrieve the menu
            ])

#     # Lotgd\Core\Tool\Commentary:
#     #     lazy: true

#     # # LoTGD PvP
#     # Lotgd\Core\Pvp\Listing:
#     #     public: true

#     # # Lotgd
#     # Lotgd\Core\Character\Stats:
#     #     public: true
    ;
};
