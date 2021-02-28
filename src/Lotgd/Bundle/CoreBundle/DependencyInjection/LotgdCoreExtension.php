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

namespace Lotgd\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class LotgdCoreExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        //-- Register parameters
        $container->setParameter($this->getAlias().'.seo.title.default', 'Legend of the Green Dragon');
        $container->setParameter($this->getAlias().'.number.format.decimal.point', '.');
        $container->setParameter($this->getAlias().'.number.format.thousands.sep', ',');
        // $container->setParameter('doctrine.orm.entity_manager.class', 'Lotgd\Core\Doctrine\ORM\EntityManager');

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));

        $loader->load('prepend/bukashk0zzz_filter.yaml');
        $loader->load('prepend/doctrine_migrations.yaml');
        $loader->load('prepend/doctrine.yaml');
        $loader->load('prepend/reset_password.yaml');
        $loader->load('prepend/security.yaml');
    }
}
