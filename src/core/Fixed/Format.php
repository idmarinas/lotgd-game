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

use Lotgd\Core\Output\Format as CoreFormat;

/**
 * This class is for format a string
 * For example: format a number or a date.
 */
class Format
{
    /**
     * Instance of Format.
     *
     * @var Lotgd\Core\Output\CoreFormat
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
     * Set a instance of Lotgd\Core\Output\CoreFormat.
     *
     * @param Lotgd\Core\Output\CoreFormat $instance
     */
    public static function instance(CoreFormat $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Format', 'LotgdFormat', false);
