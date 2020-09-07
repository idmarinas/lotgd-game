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

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Lotgd\Core\Template\Theme as TemplateTheme;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;

class Theme implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config  = $container->get('GameConfig');
        $options = $config['lotgd_core'] ?? [];

        $template = new TemplateTheme(['./vendor/symfony/twig-bridge/Resources/views/Form'], [
            'debug' => (bool) ($options['development'] ?? false),
            //-- Used dir of cache
            'cache' => 'storage/cache/template',
            //-- Used in development for reload .twig templates
            'auto_reload' => (bool) ($options['development'] ?? false),
        ]);
        $template->setContainer($container);

        // the Twig file that holds all the default markup for rendering forms
        // this file comes with TwigBridge
        $defaultFormTheme = 'semantic-ui-form-theme.html.twig';
        $formEngine       = new TwigRendererEngine([$defaultFormTheme], $template);
        $template->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine)
            {
                return new FormRenderer($formEngine);
            },
        ]));

        //-- Custom extensions
        $extensions = $config['twig_extensions'] ?? [];

        if ( ! empty($extensions) && is_array($extensions))
        {
            foreach ($extensions as $className)
            {
                $extension = new $className();

                if (method_exists($extension, 'setContainer'))
                {
                    $extension->setContainer($container);
                }

                $template->addExtension($extension);
            }
        }

        $template->addExtension(new EntryFilesTwigExtension($container));

        //-- Important
        $template->prepareTheme();

        return $template;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }
}
