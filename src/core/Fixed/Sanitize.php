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

use Lotgd\Core\Tool\Sanitize as CoreSanitize;

/**
 * This class is for sanitize a string
 * For example: sanitize a number or a date.
 */
class Sanitize
{
    /**
     * Instance of Sanitize.
     *
     * @var Lotgd\Core\Tool\CoreSanitize
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
     * Set a instance of Lotgd\Core\Tool\CoreSanitize.
     *
     * @param Lotgd\Core\Tool\CoreSanitize $instance
     */
    public static function instance(CoreSanitize $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Sanitize', 'LotgdSanitize', false);
