<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Template;

use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\DashToCamelCase;
use Laminas\Filter\Word\SeparatorToDash;
use Laminas\Filter\Word\UnderscoreToDash;
use Lotgd\Core\Exception;
use Lotgd\Core\Http\Request;
use Twig\Environment;
use Lotgd\Core\Lib\Settings;

/**
 * This class are deprecate, and only is for transition to Symfony Kernel.
 * Is replace for a new Theme System
 *
 * @deprecated 4.12.0 delete in future versions
 */
class Template extends Environment
{
    public const TEMPLATES_LAYOUT_DIR       = 'themes'; //-- Themes folder.
    public const TEMPLATES_BASE_DIR         = 'templates/lotgd'; //-- Main templates folder.
    public const TEMPLATES_CORE_BASE_DIR    = 'templates_core'; //-- Core templates of game (Not intended to be customizable)
    public const TEMPLATES_MODULES_BASE_DIR = 'templates_modules'; //-- Folder for templates of modules.

    protected $mandatoryFunctionsErrors = [];
    protected $themeName;
    protected $themeNamespace;
    protected $themefolder;
    protected $defaultSkin;
    protected $decorated;
    protected $lotgdRequest;
    protected $lotgdParams;
    protected $settings;

    /**
     * {@inheritdoc}.
     */
    public function __construct(Environment $decorated, Request $request, Params $params, Settings $settings)
    {
        $this->decorated = $decorated;
        $this->lotgdRequest = $request;
        $this->lotgdParams = $params;
        $this->settings = $settings;

        $this->prepareTheme();
        $this->updateGlobals();
    }

    /**
     * Render content in layout with active theme.
     *
     * @param array $context
     */
    public function renderLayout($context): string
    {
        return $this->decorated->render("@layout/{$this->getTheme()}", (array) $context);
    }

    /**
     * {@inheritdoc}
     * Added params to templates.
     *
     * @param string $name
     */
    public function render($name, array $context = []): string
    {
        $params = $this->lotgdParams->toArray(); //-- All parameters for template, include userPre

        $this->updateGlobals();

        $context = \array_merge($params, $context);

        return $this->decorated->render($name, (array) $context);
    }

    /**
     * Render a block of a template.
     *
     * @param string $blockName
     * @param string $template
     */
    public function renderBlock($blockName, $template, array $context = [])
    {
        $params = $this->lotgdParams->toArray(); //-- All parameters for template, include userPre

        $context = \array_merge($params, $context);

        $tpl = $this->load($template);

        return $tpl->renderBlock($blockName, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function load($name)
    {
        $this->updateGlobals();

        return $this->decorated->load($name);
    }

    /**
     * Get default skin of game.
     */
    public function getDefaultSkin(): string
    {
        if (empty($this->defaultSkin))
        {
            $theme = $this->lotgdRequest->getCookie('template');

            if ('' == $theme || ! \file_exists(static::TEMPLATES_LAYOUT_DIR."/{$theme}"))
            {
                $theme = $this->settings->getSetting('defaultskin', 'jade.html') ?: 'jade.html';
            }

            $this->defaultSkin = $theme;

            $this->settings->saveSetting('defaultskin', (string) $theme);
        }

        //-- This is necessary in case the theme is deleted
        //-- Search for a valid theme in directory
        if ( ! \file_exists(static::TEMPLATES_LAYOUT_DIR."/{$this->defaultSkin}"))
        {
            $this->defaultSkin = $this->getValidTheme();

            $this->settings->saveSetting('defaultskin', (string) $this->defaultSkin);
        }

        if ( ! $this->lotgdRequest->getCookie('template') || $this->defaultSkin != $this->lotgdRequest->getCookie('template'))
        {
            $this->lotgdRequest->setCookie('template', $this->defaultSkin);
        }

        return $this->defaultSkin;
    }

    /**
     * Change default theme
     * Need if change theme with form.
     *
     * @return $this
     */
    public function setDefaultSkin(string $theme)
    {
        $this->defaultSkin = $theme;

        bdump($theme, 'Set default theme');

        $this->prepareTheme();

        return $this;
    }

    /**
     * Get active theme.
     *
     * @return string
     */
    public function getThemeNamespace()
    {
        return $this->themeNamespace;
    }

    /**
     * Get namespace for theme.
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->themeName;
    }

    /**
     * Get name of theme folder.
     */
    public function getThemeFolder(): string
    {
        return $this->themefolder;
    }

    /**
     * Prepare template for use.
     */
    public function prepareTheme()
    {
        $this->themeName = $this->getDefaultSkin();

        if (empty($this->themefolder) || empty($this->themeNamespace))
        {
            //-- Prepare name folder of theme, base on filename of theme
            $this->themefolder = \pathinfo($this->themeName, PATHINFO_FILENAME); //-- Delete extension
            $filterChain       = new FilterChain();
            $filterChain
                ->attach(new StringToLower())
                ->attach(new SeparatorToDash())
                ->attach(new UnderscoreToDash())
            ;

            $this->themefolder    = $filterChain->filter($this->themefolder);
            $filter               = new DashToCamelCase();
            $this->themeNamespace = $filter->filter($this->themefolder);

            //-- Add theme namespace to loader
            $chain = $this->getLoader()->getLoaders();
            $chain[0]->setThemeNamespace($this->getThemeNamespace());
        }
    }

    /**
     * Search for a valid theme if removed.
     *
     * @throws RuntimeException
     */
    protected function getValidTheme(): string
    {
        // A generic way of allowing a theme to be selected.
        $skins  = [];
        $handle = @\opendir(static::TEMPLATES_LAYOUT_DIR);

        while (false !== ($file = @\readdir($handle)))
        {
            if ('html' == \pathinfo($file, PATHINFO_EXTENSION))
            {
                $skins[] = $file;

                break; //-- We have 1 theme, no need more
            }
        }

        //-- Not found any valid theme
        if (empty($skins))
        {
            throw new Exception\RuntimeException(\sprintf('Not found a valid "theme.html" file in "%s" folder.', static::TEMPLATES_LAYOUT_DIR), 1);
        }

        return $skins[0];
    }

    /**
     * Update globals parameters.
     */
    protected function updateGlobals(): void
    {
        global $session;

        $user   = $session['user'] ?? [];
        $sesion = $session         ?? [];
        unset($sesion['user'], $user['password']);

        $this->decorated->addGlobal('user', $user); //-- Actual user data for this call
        $this->decorated->addGlobal('session', $sesion); //-- Actual session data for this call
    }

    /**
     * sobree scribir las funciones originales
     */

    public function getBaseTemplateClass()
    {
        return $this->decorated->getBaseTemplateClass();
    }

    public function setBaseTemplateClass($class)
    {
        $this->decorated->setBaseTemplateClass($class);
    }

    public function enableDebug()
    {
        $this->decorated->enableDebug();
    }

    public function disableDebug()
    {
        $this->decorated->disableDebug();
    }

    public function isDebug()
    {
        return $this->decorated->isDebug();
    }

    public function enableAutoReload()
    {
        $this->decorated->enableAutoReload();
    }

    public function disableAutoReload()
    {
        $this->decorated->disableAutoReload();
    }

    public function isAutoReload()
    {
        return $this->decorated->isAutoReload();
    }

    public function enableStrictVariables()
    {
        $this->decorated->enableStrictVariables();
    }

    public function disableStrictVariables()
    {
        $this->decorated->disableStrictVariables();
    }

    public function isStrictVariables()
    {
        $this->decorated->isStrictVariables();
    }

    public function getCache($original = true)
    {
        return $this->decorated->getCache($original);
    }

    public function setCache($cache)
    {
        $this->decorated->setCache($cache);
    }

    public function getTemplateClass($name, $index = null)
    {
        return $this->decorated->getTemplateClass($name, $index);
    }

    public function display($name, array $context = [])
    {
        $this->load($name)->display($context);
    }

    public function loadTemplate($name, $index = null)
    {
        return $this->decorated->loadTemplate($name, $index);
    }

    public function loadClass($cls, $name, $index = null)
    {
        return $this->decorated->loadClass($cls, $name, $index);
    }

    public function createTemplate($template, string $name = null)
    {
        $this->decorated->createTemplate($template, $name);
    }

    public function isTemplateFresh($name, $time)
    {
        $this->decorated->isTemplateFresh($name, $time);
    }

    public function resolveTemplate($names)
    {
        return $this->decorated->resolveTemplate($names);
    }

    public function setLexer($lexer)
    {
        $this->decorated->setLexer($lexer);
    }

    public function tokenize($source)
    {
        $this->decorated->tokenize($source);
    }

    public function setParser($parser)
    {
        $this->decorated->setParser($parser);
    }

    public function parse($stream)
    {
        return $this->decorated->parse($stream);
    }

    public function setCompiler($compiler)
    {
        $this->decorated->setCompiler($compiler);
    }

    public function compile($node)
    {
        return $this->decorated->compile($node);
    }

    public function compileSource($source)
    {
        return $this->decorated->compileSource($source);
    }

    public function setLoader($loader)
    {
        $this->decorated->setLoader($loader);
    }

    public function getLoader()
    {
        return $this->decorated->getLoader();
    }

    public function setCharset($charset)
    {
        $this->decorated->setCharset($charset);
    }

    public function getCharset()
    {
        return $this->decorated->getCharset();
    }

    public function hasExtension($class)
    {
        return $this->decorated->hasExtension($class);
    }

    public function addRuntimeLoader($loader)
    {
        $this->decorated->addRuntimeLoader($loader);
    }

    public function getExtension($class)
    {
        return $this->decorated->getExtension($class);
    }

    public function getRuntime($class)
    {
        $this->decorated->getRuntime($class);
    }

    public function addExtension($extension)
    {
        $this->decorated->addExtension($extension);
    }

    public function setExtensions(array $extensions)
    {
        $this->decorated->setExtensions($extensions);
    }

    public function getExtensions()
    {
        return $this->extensionSet->getExtensions();
    }

    public function addTokenParser($parser)
    {
        $this->decorated->addTokenParser($parser);
    }

    public function getTokenParsers()
    {
        return $this->decorated->getTokenParsers();
    }

    public function getTags()
    {
        $this->decorated->getTags();
    }

    public function addNodeVisitor($visitor)
    {
        $this->decorated->addNodeVisitor($visitor);
    }

    public function getNodeVisitors()
    {
        return $this->decorated->getNodeVisitors();
    }

    public function addFilter($filter)
    {
        $this->decorated->addFilter($filter);
    }

    public function getFilter($name)
    {
        return $this->decorated->getFilter($name);
    }

    public function registerUndefinedFilterCallback(callable $callable)
    {
        $this->decorated->registerUndefinedFilterCallback($callable);
    }

    public function getFilters()
    {
        return $this->decorated->getFilters();
    }

    public function addTest($test)
    {
        $this->decorated->addTest($test);
    }

    public function getTests()
    {
        return $this->decorated->getTests();
    }

    public function getTest($name)
    {
        return $this->decorated->getTest($name);
    }

    public function addFunction($function)
    {
        $this->decorated->addFunction($function);
    }

    public function getFunction($name)
    {
        return $this->decorated->getFunction($name);
    }

    public function registerUndefinedFunctionCallback(callable $callable)
    {
        $this->decorated->registerUndefinedFunctionCallback($callable);
    }

    public function getFunctions()
    {
        return $this->decorated->getFunctions();
    }

    public function addGlobal($name, $value)
    {
        $this->decorated->addGlobal($name, $value);
    }

    public function getGlobals()
    {
        return $this->decorated->getGlobals();
    }

    public function mergeGlobals(array $context)
    {
        return $this->decorated->mergeGlobals($context);
    }

    public function getUnaryOperators()
    {
        return $this->decorated->getUnaryOperators();
    }

    public function getBinaryOperators()
    {
        return $this->decorated->getBinaryOperators();
    }
}
