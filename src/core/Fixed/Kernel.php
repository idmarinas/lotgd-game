<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Fixed;

use Lotgd\Core\Kernel as CoreKernel;

class Kernel
{
    /**
     * Intance of Kernel
     *
     * @var CoreKernel
     */
    protected static $instance;

    /**
     * Add support for magic static method calls.
     *
     * @param mixed  $method
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

        throw new \BadMethodCallException("Undefined method '{$method}'. The method name must be one of '{$methods}'");
    }

    /**
     * Set instance of Kernel.
     */
    public static function instance(CoreKernel $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Kernel', 'LotgdKernel', false);
