<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migrating/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Log as CoreLog;

class Log
{
    /** @var \Lotgd\Core\Log */
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

        $methods = \implode(', ', \get_class_methods(self::$instance));

        throw new \BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }

    /**
     * @param \Lotgd\Core\Log $instance
     */
    public static function instance(CoreLog $instance)
    {
        self::$instance = $instance;
    }
}

\class_alias('Lotgd\Core\Fixed\Log', 'LotgdLog', false);
