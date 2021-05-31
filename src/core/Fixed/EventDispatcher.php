<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.0
 */

namespace Lotgd\Core\Fixed;

use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher as CoreEventDispatcher;

class EventDispatcher
{
    /**
     * Instance.
     *
     * @var CoreEventDispatcher
     */
    protected static $instance;

    /**
     * Add support for magic static method calls.
     *
     * @param mixed $method
     * @param array $arguments
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
     * Set instance of EventManager.
     *
     * @param CoreEventDispatcher $container
     */
    public static function instance(CoreEventDispatcher $instance)
    {
        self::$instance = $instance;
    }
}

\class_alias('Lotgd\Core\Fixed\EventDispatcher', 'LotgdEventDispatcher', false);
