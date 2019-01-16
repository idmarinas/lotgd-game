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

use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\SeparatorToDash;
use Zend\Filter\Word\UnderscoreToDash;

class Theme extends Base
{
    use \Lotgd\Core\Pattern\Container;

    protected $themeName;
    protected $themefolder;
    protected $defaultSkin;

    public function __construct(array $loader = [], array $options = [])
    {
        //-- Merge loaders
        $loader = array_merge(['themes', 'templates'], $loader);

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

        $folder = $this->themefolder.'/templates';

        $userPre = $html['userPre'] ?? [];
        $user = $session['user'] ?? [];
        unset($user['password']);
        $sesion = $session ?? [];
        unset($user['user']);

        $context = array_merge(['userPre' => $userPre, 'user' => $user, 'session' => $sesion], $context);

        return $this->render("{$folder}/{$name}", $context);
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
        $request = $this->getContainer(\Lotgd\Core\Http::class);
        $cookie = $request->getCookie();

        if (empty($this->defaultSkin))
        {
            $settings = $this->getContainer(\Lotgd\Core\Lib\Settings::class);

            $theme = $cookie->offsetExists('template') ? $cookie->offsetExists('template') : '';

            if ('' == $theme || ! file_exists("themes/$theme"))
            {
                $theme = $settings->getSetting('defaultskin', 'jade.html') ?: 'jade.html';
            }

            $this->defaultSkin = $theme;

            $settings->saveSetting('defaultskin', $theme);
        }

        //-- This is necessary in case the theme is deleted
        //-- Search for a valid theme in directory
        if (! file_exists("themes/{$this->defaultSkin}"))
        {
            $this->defaultSkin = $this->getValidTheme();

            $settings->saveSetting('defaultskin', $this->defaultSkin);
        }

        if ($cookie->offsetExists('template') || '' == $cookie->offsetExists('template'))
        {
            $cookie->offsetSet('template', $theme);
        }

        return $this->defaultSkin;
    }

    /**
     * Prepare template for use.
     */
    public function prepareTheme()
    {
        global $y, $z, $y2, $z2, $lc, $x;

        $this->themeName = $this->getDefaultSkin();

        if (empty($this->themefolder) && false === strpos($this->themefolder, $this->themeName))
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
     * @return array
     */
    private function getValidTheme(): array
    {
        // A generic way of allowing a theme to be selected.
        $skins = [];
        $handle = @opendir('themes');

        while (false !== ($file = @readdir($handle)))
        {
            if (strpos($file, '.htm') > 0)
            {
                $skins[] = $file;

                break; //-- We have 1 theme, no need more
            }
        }

        return $skins;
    }
}
