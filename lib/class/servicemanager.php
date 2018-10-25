<?php

use Lotgd\Core\ServiceManager;

class LotgdLocator
{
    protected static $serviceManager;

    /**
     * Get a shared instance of service
     *
     * @param string $name
     *
     * @return void
     */
    public static function get(string $name)
    {
        return self::$serviceManager->get($name);
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
        return self::$serviceManager->build($name);
    }

    /**
     * Get service manager
     *
     * @return \Lotgd\Core\ServiceManager
     */
    public static function getServiceManager()
    {
        return self::$serviceManager;
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
        self::$serviceManager = $sm;
    }
}

//-- Prepare service manager
LotgdLocator::setServiceManager(new Lotgd\Core\ServiceManager(require 'config/config.php'));
