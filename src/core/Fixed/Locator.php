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
     * Instance of ServiceManager
     *
     * @var Lotgd\Core\ServiceManager
     */
    protected static $sm;

    /**
     * Get a shared instance of service
     *
     * @param string $name
     *
     * @return void
     */
    public static function get(string $name)
    {
        return self::$sm->get($name);
    }

    /**
     * Get a discrete instance of service
     *
     * @param string $name
     *
     * @return void
     */
    public static function build(string $name)
    {
        return self::$sm->build($name);
    }

    /**
     * Get service manager
     *
     * @return \Lotgd\Core\ServiceManager
     */
    public static function getServiceManager()
    {
        return self::$sm;
    }

    /**
     * Set service manager for the game
     *
     * @param \Lotgd\Core\ServiceManager $sm
     *
     * @return void
     */
    public static function setServiceManager(ServiceManager $sm)
    {
        self::$sm = $sm;
    }
}

class_alias('Lotgd\Core\Fixed\Locator', 'LotgdLocator', false);
