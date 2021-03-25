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

use Lotgd\Bundle\CoreBundle\Block\DonationButtonsBlock;
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

        //-- Tools
        ->set('lotgd_bundle.format', Format::class)
            ->args([
                new ReferenceConfigurator('translator'),
                new ReferenceConfigurator(Code::class),
                new ReferenceConfigurator(Color::class)
            ])
            ->call('setDecPoint', ['%lotgd_bundle.number.format.decimal.point%'])
            ->call('setThousandsSep', ['%lotgd_bundle.number.format.thousands.sep%'])
            ->public()
        ->alias(Format::class, 'lotgd_bundle.format')

        ->set('lotgd_bundle.censor', Censor::class)
            ->lazy()
            ->args([
                '%kernel.default_locale%',
                '%kernel.project_dir%'
            ])
            ->public()
        ->alias(Censor::class, 'lotgd_bundle.censor')

        //-- Twig extensions
        ->set(ByteUnitsExtension::class)
            ->tag('twig.extension')
            ->lazy()

        //-- Menu
        ->set('lotgd_bundle.menu_builder', MenuBuilder::class)
            ->args([
                new ReferenceConfigurator('knp_menu.factory'),
                new ReferenceConfigurator('event_dispatcher')
            ])
            ->tag('knp_menu.menu_builder', [
                'method' => 'createMenuCore',
                'alias' => 'lotgd_bundle.menu' //-- The alias is what is used to retrieve the menu
            ])

        //-- Blocks
        ->set('lotgd.core.template.block.donation.buttons', DonationButtonsBlock::class)
            ->args([new ReferenceConfigurator('twig')])
            ->call('setRequest', [new ReferenceConfigurator('request_stack')])
            ->call('setRepository', [new ReferenceConfigurator('Lotgd\Bundle\UserBundle\Repository\UserRepository')])
            ->call('setRouter', [new ReferenceConfigurator('router')])
            ->call('setSession', [new ReferenceConfigurator('session')])
            ->call('setSecurity', [new ReferenceConfigurator('security.helper')])
            ->tag('sonata.block')
    ;
};
