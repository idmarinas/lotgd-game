<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 2.7.0
 */

namespace Lotgd\Core\Factory\Template;

use Lotgd\Core\Component;
use Lotgd\Core\Twig\Extension;
use Lotgd\Core\Template\Theme as TemplateTheme;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Theme implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('GameConfig')['lotgd_core'] ?? [];
        $cacheDir = trim($options['cache']['base_cache_dir'] ?? 'cache/', '/');

        $template = new TemplateTheme([], [
            //-- Used dir of cache
            'cache' => "{$cacheDir}/templates",
            //-- Used in development for reload .twig templates
            'auto_reload' => (bool) ($options['development'] ?? false)
        ]);
        $template->setContainer($container);
        $template->addExtension(new Extension\FlashMessages($container->get(Component\FlashMessages::class)));
        $template->prepareTheme();

        return $template;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
