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

use Lotgd\Core\Navigation\Navigation as CoreNavigation;

class Navigation
{
    /**
     * Instance of Navigation
     *
     * @var Lotgd\Core\Navigation\Navigation
     */
    protected static $instance;

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function addHeader($header, $options = [])
    {
        return self::$instance->addHeader($header, $options);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function addHeaderNotl($header, $options = [])
    {
        return self::$instance->addHeaderNotl($header, $options);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function addNav($label, $link = null, $options = [])
    {
        return self::$instance->addNav($label, $link, $options);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function addNavNotl($label, $link = null, $options = [])
    {
        return self::$instance->addNavNotl($label, $link, $options);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function addNavAllow($link)
    {
        return self::$instance->addNavAllow($link);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function setTextDomain($domain = null)
    {
        return self::$instance->setTextDomain($domain);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function blockLink($link)
    {
        return self::$instance->blockLink($link);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function unBlockLink($link)
    {
        return self::$instance->unBlockLink($link);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function blockPartialLink($link)
    {
        return self::$instance->blockPartialLink($link);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function unBlockPartialLink($link)
    {
        return self::$instance->unBlockPartialLink($link);
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function superuser()
    {
        return self::$instance->superuser();
    }

    /**
     * @see Lotgd\Core\Navigation\Navigation
     */
    public static function checkNavs()
    {
        return self::$instance->checkNavs();
    }

    /**
     * Set instance of Navigation
     *
     * @param CoreNavigation $container
     */
    public static function instance(CoreNavigation $instance)
    {
        self::$instance = $instance;
    }
}

class_alias('Lotgd\Core\Fixed\Navigation', 'LotgdNavigation', false);
