<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.1.0
 */

namespace Lotgd\Core\Fixed;

use Laminas\Cache\Storage\StorageInterface;

\trigger_error(\sprintf(
    'Class %s is deprecated, please use Doctrine instead. "$cache = \LotgdKernel::get("cache.app")"',
    Cache::class
), E_USER_DEPRECATED);

/**
 * @deprecated 4.9.0
 */
class Cache
{
    /**
     * Instance of Cache.
     *
     * @var Laminas\Cache\Storage\StorageInterface
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
     * Set instance of Cache.
     *
     * @param Laminas\Cache\Storage $instance
     */
    public static function instance(StorageInterface $instance)
    {
        self::$instance = $instance;
    }
}

\class_alias('Lotgd\Core\Fixed\Cache', 'LotgdCache', false);
