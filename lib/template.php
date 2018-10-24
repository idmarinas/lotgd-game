<?php

use Lotgd\Core\Template\Theme;

class LotgdTheme
{
    protected static $wrapper;

    /**
     * Render a theme
     * Used in pageparts.php for render a page.
     */
    public static function renderTheme($context)
    {
        return self::$wrapper->renderTheme($context);
    }

    /**
     * Renders a template of the theme.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public static function renderThemeTemplate($name, $context)
    {
        return self::$wrapper->renderThemeTemplate($name, $context);
    }

    /**
     * Renders a template of LOTGD.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public static function renderLotgdTemplate($name, $context)
    {
        return self::$wrapper->renderLotgdTemplate($name, $context);
    }

    /**
     * Get active theme.
     *
     * @return string
     */
    public static function getTheme()
    {
        return self::$wrapper->getTheme();
    }

    /**
     * Set wrapper of Theme.
     *
     * @param \Lotgd\Core\Template\Theme $wrapper
     */
    public static function wrapper(Theme $wrapper)
    {
        self::$wrapper = $wrapper;
    }
}
