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

use Lotgd\Core\Component\FlashMessages as ComponentFlashMessages;

class FlashMessages
{
    /**
     * Instance of FlashMessages
     *
     * @var Lotgd\Core\Component\FlashMessages
     */
    protected static $container;

    /**
     * @see Lotgd\Core\Component\FlashMessages
     */
    public static function addMessage(string $message, $type = null)
    {
        return self::$container->addMessage($message, $type);
    }

    /**
     * @see Lotgd\Core\Component\FlashMessages
     */
    public static function addInfoMessage(string $message)
    {
        return self::$container->addInfoMessage($message);
    }

    /**
     * @see Lotgd\Core\Component\FlashMessages
     */
    public static function addSuccessMessage(string $message)
    {
        return self::$container->addSuccessMessage($message);
    }

    /**
     * @see Lotgd\Core\Component\FlashMessages
     */
    public static function addErrorMessage(string $message)
    {
        return self::$container->addErrorMessage($message);
    }

    /**
     * @see Lotgd\Core\Component\FlashMessages
     */
    public static function addWarningMessage(string $message)
    {
        return self::$container->addWarningMessage($message);
    }

    /**
     * Set container of FlashMessages
     *
     * @param ComponentFlashMessages $container
     */
    public static function setContainer(ComponentFlashMessages $container)
    {
        self::$container = $container;
    }
}

class_alias('Lotgd\Core\Fixed\FlashMessages', 'LotgdFlashMessages', false);
