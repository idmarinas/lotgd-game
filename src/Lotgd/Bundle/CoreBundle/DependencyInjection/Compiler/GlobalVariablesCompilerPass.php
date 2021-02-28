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

namespace Lotgd\Bundle\CoreBundle\DependencyInjection\Compiler;

use Lotgd\Bundle\Kernel;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;

final class GlobalVariablesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('twig')
            ->addMethodCall('addGlobal', ['lotgd_title', new Parameter('lotgd_core.seo.title.default')])
            ->addMethodCall('addGlobal', ['lotgd_menu', 'lotgd_core.menu'])
            ->addMethodCall('addGlobal', ['lotgd_version', Kernel::VERSION])
        ;
    }
}
