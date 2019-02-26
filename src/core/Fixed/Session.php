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

use  Lotgd\Core\Session as CoreSession;

class Session
{
    /**
     * Instance of Session.
     *
     * @var Lotgd\Core\Session
     */
    protected static $instance;

    /**
     * @see Lotgd\Core\Session
     */
    public static function bootstrapSession($force = null)
    {
        return self::$instance->bootstrapSession($force);
    }

    public static function sessionLogOut()
    {
        return self::$instance->sessionLogOut();
    }

    /**
     * Set instance of Navigation.
     *
     * @param CoreSession $container
     */
    public static function instance(CoreSession $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Session', 'LotgdSession', false);
