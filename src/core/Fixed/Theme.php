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
     * Add support for magic static method calls.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed the returned value from the resolved method
     */
    public static function __callStatic($method, $arguments)
    {
        if (\method_exists(self::$wrapper, $method))
        {
            return self::$wrapper->{$method}(...$arguments);
        }

        $methods = implode(', ', get_class_methods(self::$wrapper));

        throw new \BadMethodCallException("Undefined method '$method'. The method name must be one of '$methods'");
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
