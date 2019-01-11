<?php

use Lotgd\Core\ServiceManager;

class LotgdLocator
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

//-- Prepare service manager
LotgdLocator::setServiceManager(new ServiceManager());
