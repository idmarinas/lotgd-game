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
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Twig\Extension\ProfilerExtension;
use Twig\Profiler\Profile;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

class Theme implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config   = $container->get('GameConfig');
        $opts     = $config['lotgd_core'] ?? [];
        $packages = $container->get('webpack_encore.packages');
        $isDevelopment = (bool) ($opts['development'] ?? false);

        $templateSystem = new TemplateTheme([], [
            'debug' => $isDevelopment,
            //-- Used dir of cache
            'cache' => 'storage/cache/template',
            //-- Used in development for reload .twig templates
            'auto_reload' => $isDevelopment,
        ]);
        $templateSystem->setContainer($container);

        //-- Added templates Symfony Forms with namespace
        $templateSystem->getLoader()->prependPath('./vendor/symfony/twig-bridge/Resources/views/Form', 'symfonyForm');

        // the Twig file that holds all the default markup for rendering forms
        // this file comes with TwigBridge
        $defaultFormTheme = '{theme}/form/semantic-ui-form-theme.html.twig';
        $formEngine       = new TwigRendererEngine([$defaultFormTheme], $templateSystem);
        $templateSystem->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine)
            {
                return new FormRenderer($formEngine);
            },
        ]));

        //-- Register globals params
        $globals = $config['twig_global_params'] ?? [];
        $globals = array_merge($globals, ['enviroment' => $isDevelopment ? 'dev' : 'prod' ]);
        $this->registerGlobals($globals, $templateSystem);

        //-- Custom extensions
        $extensions = $config['twig_extensions'] ?? [];
        $this->addTwigExtensions($extensions, $templateSystem, $container);

        $templateSystem->addExtension(new EntryFilesTwigExtension($container));
        $templateSystem->addExtension(new AssetExtension($packages));

        //-- Templates path
        $tplPaths = $config['twig_templates_paths'] ?? [];
        $this->addTemplatePaths($tplPaths, $templateSystem);

        if ($isDevelopment)
        {
            $profile = new Profile();
            $templateSystem->addExtension(new ProfilerExtension($profile));

            \Idmarinas\TracyPanel\TwigBar::init($profile);
        }

        //-- Important
        $templateSystem->prepareTheme();
        //-- Add theme namespace to loader
        $templateSystem->getLoader()->setThemeNamespace($templateSystem->getThemeNamespace());

        return $templateSystem;
    }

    public function createService(ServiceLocatorInterface $services, $canonicalName = null, $requestedName = null)
    {
        return $this($services, $requestedName);
    }

    //-- Add twig extensions
    private function addTwigExtensions($extensions, TemplateTheme &$templateSystem, $container): void
    {
        if ( ! empty($extensions) && \is_array($extensions))
        {
            foreach ($extensions as $className)
            {
                //-- Allows to override/remove extensions.
                if (empty($className))
                {
                    continue;
                }

                $extension = new $className();

                if (\method_exists($extension, 'setContainer'))
                {
                    $extension->setContainer($container);
                }

                $templateSystem->addExtension($extension);
            }
        }
    }

    /**
     * Add template paths.
     * With using of `prependPath` add first new path to search in this folders first.
     * Them fallback to other paths.
     *
     * @param mixed $tplPaths
     */
    private function addTemplatePaths($tplPaths, TemplateTheme &$templateSystem): void
    {
        if ( ! empty($tplPaths) && \is_array($tplPaths))
        {
            foreach ($tplPaths as $path => $namespace)
            {
                if (empty($namespace))
                {
                    $templateSystem->getLoader()->prependPath($path);

                    continue;
                }

                $templateSystem->getLoader()->addPath($path, $namespace);
            }
        }
    }

    /**
     * Register global parameters.
     *
     * @param array $globals
     */
    private function registerGlobals($globals, TemplateTheme &$templateSystem)
    {
        foreach ($globals as $name => $value)
        {
            $templateSystem->addGlobal($name, $value);
        }
    }
}
