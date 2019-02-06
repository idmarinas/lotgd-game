<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 3.0.0
 */

namespace Lotgd\Core\Template;

use Lotgd\Core\Exception;
use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\SeparatorToDash;
use Zend\Filter\Word\UnderscoreToDash;

class Theme extends Base
{
    const TEMPLATES_BASE_DIR = 'data/templates';

    protected $themeName;
    protected $themefolder;
    protected $defaultSkin;

    public function __construct(array $loader = [], array $options = [])
    {
        //-- Merge loaders
        $loader = array_merge([static::TEMPLATES_BASE_DIR], $loader);

        parent::__construct($loader, $options);
    }

    /**
     * Render a theme
     * Used in pageparts.php for render a page.
     */
    public function renderTheme($context)
    {
        return $this->render($this->getTheme(), $context);
    }

    /**
     * Renders a template of the theme.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function renderThemeTemplate($name, $context)
    {
        global $html, $session;

        $userPre = $html['userPre'] ?? [];
        $user = $session['user'] ?? [];
        unset($user['password']);
        $sesion = $session ?? [];
        unset($user['user']);

        $context = array_merge(['userPre' => $userPre, 'user' => $user, 'session' => $sesion], $context);

        return $this->render("{$this->themefolder}/{$name}", $context);
    }

    /**
     * Renders a template of LOTGD.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function renderLotgdTemplate($name, $context)
    {
        return $this->render($name, $context);
    }

    /**
     * Get active theme.
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->themeName;
    }

    /**
     * Get default skin of game.
     *
     * @return string
     */
    public function getDefaultSkin(): string
    {
        if (empty($this->defaultSkin))
        {
            $settings = $this->getContainer(\Lotgd\Core\Lib\Settings::class);

            $theme = \LotgdHttp::getCookie('template');

            if ('' == $theme || ! file_exists(static::TEMPLATES_BASE_DIR."/$theme"))
            {
                $theme = $settings->getSetting('defaultskin', 'jade.html') ?: 'jade.html';
            }

            $this->defaultSkin = $theme;

            $settings->saveSetting('defaultskin', (string) $theme);
        }

        //-- This is necessary in case the theme is deleted
        //-- Search for a valid theme in directory
        if (! file_exists(static::TEMPLATES_BASE_DIR."/{$this->defaultSkin}"))
        {
            $this->defaultSkin = $this->getValidTheme();

            $settings->saveSetting('defaultskin', (string) $this->defaultSkin);
        }

        if (! \LotgdHttp::getCookie('template') || $this->defaultSkin != \LotgdHttp::getCookie('template'))
        {
            \LotgdHttp::setCookie('template', $this->defaultSkin);
        }

        return $this->defaultSkin;
    }

    /**
     * Change default theme
     * Need if change theme with form.
     *
     * @param string $theme
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
     * Prepare template for use.
     */
    public function prepareTheme()
    {
        $this->themeName = $this->getDefaultSkin();

        if (empty($this->themefolder) || false === strpos($this->themeName, $this->themefolder))
        {
            //-- Prepare name folder of theme, base on filename of theme
            $this->themefolder = pathinfo($this->themeName, PATHINFO_FILENAME); //-- Delete extension
            $filterChain = new FilterChain();
            $filterChain
                ->attach(new StringToLower())
                ->attach(new SeparatorToDash())
                ->attach(new UnderscoreToDash())
            ;

            $this->themefolder = $filterChain->filter($this->themefolder);
        }
    }

    /**
     * Search for a valid theme if removed.
     *
     * @throws RuntimeException
     *
     * @return string
     */
    private function getValidTheme(): string
    {
        // A generic way of allowing a theme to be selected.
        $skins = [];
        $handle = @opendir(static::TEMPLATES_BASE_DIR);

        while (false !== ($file = @readdir($handle)))
        {
            if ('html' == pathinfo($file, PATHINFO_EXTENSION))
            {
                $skins[] = $file;

                break; //-- We have 1 theme, no need more
            }
        }

        //-- Not found any valid theme
        if (empty($skins))
        {
            throw new Exception\RuntimeException(sprintf('Not found a valid "theme.html" file in "%s" folder.', static::TEMPLATES_BASE_DIR), 1);
        }

        return $skins[0];
    }
}
