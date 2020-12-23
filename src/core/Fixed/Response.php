<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Http\Response as CoreResponse;

class Response
{
    /**
     * Instance of Response.
     *
     * @var Lotgd\Core\Http\Response
     */
    protected static $instance;

    /**
     * Add support for magic static method calls.
     *
     * @param string $name
     * @param array  $arguments
     * @param mixed  $method
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

        throw new \BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }

    /**
     * Set instance of Response.
     *
     * @param CoreResponse $container
     */
    public static function instance(CoreResponse $instance)
    {
        self::$instance = $instance;
    }

    /**
     * Get the instance.
     */
    public static function _i()
    {
        return self::$instance;
    }
}

class_alias('Lotgd\Core\Fixed\Response', 'LotgdResponse', false);
