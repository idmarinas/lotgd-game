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
use Lotgd\Core\Pattern;
use Lotgd\Core\Twig\Loader\LotgdFilesystemLoader;
use Twig\Environment;

class Template extends Environment
{
    use Pattern\Container;
    use Pattern\Template;

    const TEMPLATES_LAYOUT_DIR       = 'themes'; //-- Themes folder.
    const TEMPLATES_BASE_DIR         = 'templates/lotgd'; //-- Main templates folder.
    const TEMPLATES_CORE_BASE_DIR    = 'templates_core'; //-- Core templates of game (Not intended to be customizable)
    const TEMPLATES_MODULES_BASE_DIR = 'templates_modules'; //-- Folder for templates of modules.

    protected $mandatoryFunctionsErrors = [];
    protected $themeName;
    protected $themeNamespace;
    protected $themefolder;
    protected $defaultSkin;

    /**
     * {@inheritdoc}.
     */
    public function __construct(array $loader = [], array $options = [])
    {
        //-- Merge options
        $default = [
            'cache'      => 'storage/cache/templates',
            'autoescape' => false,
        ];
        $options = \array_merge($default, $options);

        \array_push($loader, static::TEMPLATES_BASE_DIR); // Main templates is always last, is the last area where Twig will look
        \array_push($loader, 'templates'); // Compatibility with modules (temporal , remove in future before 5.0.0)
        $loader = new LotgdFilesystemLoader($loader);

        //-- Added path to templates modules
        $loader->addPath(static::TEMPLATES_MODULES_BASE_DIR, 'module');

        //-- Added path to core templates
        $loader->addPath(static::TEMPLATES_CORE_BASE_DIR, 'core');

        //-- Added path to layout templates (themes)
        $loader->addPath(static::TEMPLATES_LAYOUT_DIR, 'layout');

        parent::__construct($loader, $options);

        $this->updateGlobals();
    }

    /**
     * Render content in layout with active theme.
     *
     * @param array $context
     */
    public function renderLayout($context): string
    {
        return $this->render("@layout/{$this->getTheme()}", (array) $context);
    }

    /**
     * {@inheritdoc}
     * Added params to templates.
     *
     * @param string $name
     */
    public function render($name, array $context = []): string
    {
        $params = $this->getTemplateParams()->toArray(); //-- All parameters for template, include userPre

        $this->updateGlobals();

        $context = \array_merge($params, $context);

        return parent::render($name, (array) $context);
    }

    /**
     * Render a block of a template.
     *
     * @param string $blockName
     * @param string $template
     */
    public function renderBlock($blockName, $template, array $context = [])
    {
        $params = $this->getTemplateParams()->toArray(); //-- All parameters for template, include userPre

        $context = \array_merge($params, $context);

        $tpl = $this->load($template);

        return $tpl->renderBlock($blockName, $context);
    }

    /**
     * @inheritDoc
     */
    public function load($name)
    {
        $this->updateGlobals();

        return parent::load($name);
    }

    /**
     * Get default skin of game.
     */
    public function getDefaultSkin(): string
    {
        if (empty($this->defaultSkin))
        {
            $settings = $this->getContainer(\Lotgd\Core\Lib\Settings::class);

            $theme = \LotgdRequest::getCookie('template');

            if ('' == $theme || ! \file_exists(static::TEMPLATES_LAYOUT_DIR."/{$theme}"))
            {
                $theme = $settings->getSetting('defaultskin', 'jade.html') ?: 'jade.html';
            }

            $this->defaultSkin = $theme;

            $settings->saveSetting('defaultskin', (string) $theme);
        }

        //-- This is necessary in case the theme is deleted
        //-- Search for a valid theme in directory
        if ( ! \file_exists(static::TEMPLATES_LAYOUT_DIR."/{$this->defaultSkin}"))
        {
            $this->defaultSkin = $this->getValidTheme();

            $settings->saveSetting('defaultskin', (string) $this->defaultSkin);
        }

        if ( ! \LotgdRequest::getCookie('template') || $this->defaultSkin != \LotgdRequest::getCookie('template'))
        {
            \LotgdRequest::setCookie('template', $this->defaultSkin);
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

        $this->addGlobal('user', $user); //-- Actual user data for this call
        $this->addGlobal('session', $sesion); //-- Actual session data for this call
    }
}
