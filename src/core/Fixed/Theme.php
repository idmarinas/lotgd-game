<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 3.0.0
 */

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Template\Theme as CoreTheme;

class Theme
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
     * @param CoreTheme $wrapper
     */
    public static function wrapper(CoreTheme $wrapper)
    {
        self::$wrapper = $wrapper;
    }
}

class_alias('Lotgd\Core\Fixed\Theme', 'LotgdTheme', false);
