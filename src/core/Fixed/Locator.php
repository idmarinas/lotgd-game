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

use Lotgd\Core\ServiceManager;

class Locator
{
    /**
     * Instance of ServiceManager.
     *
     * @var Lotgd\Core\ServiceManager
     */
    protected static $sm;

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
        if (\method_exists(self::$sm, $method))
        {
            return self::$sm->{$method}(...$arguments);
        }

        $methods = implode(', ', get_class_methods(self::$sm));

        throw new \BadMethodCallException("Undefined method '$method'. The method name must be one of '$methods'");
    }

    /**
     * Get service manager.
     *
     * @return \Lotgd\Core\ServiceManager
     */
    public static function getServiceManager()
    {
        return self::$sm;
    }

    /**
     * Set service manager for the game.
     *
     * @param \Lotgd\Core\ServiceManager $sm
     */
    public static function setServiceManager(ServiceManager $sm)
    {
        self::$sm = $sm;
    }
}

class_alias('Lotgd\Core\Fixed\Locator', 'LotgdLocator', false);
