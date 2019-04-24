<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Navigation\Navigation as CoreNavigation;

class Navigation
{
    /**
     * Instance of Navigation
     *
     * @var Lotgd\Core\Navigation\Navigation
     */
    protected static $instance;

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
        if (\method_exists(self::$instance, $method))
        {
            return self::$instance->{$method}(...$arguments);
        }

        $methods = implode(', ', get_class_methods(self::$instance));

        throw new \BadMethodCallException("Undefined method '$method'. The method name must be one of '$methods'");
    }

    /**
     * Set instance of Navigation
     *
     * @param CoreNavigation $instance
     */
    public static function instance(CoreNavigation $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Navigation', 'LotgdNavigation', false);
