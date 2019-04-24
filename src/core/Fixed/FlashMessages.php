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
     * Instance of FlashMessages.
     *
     * @var Lotgd\Core\Component\FlashMessages
     */
    protected static $container;

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
        if (\method_exists(self::$container, $method))
        {
            return self::$container->{$method}(...$arguments);
        }

        $methods = implode(', ', get_class_methods(self::$container));

        throw new \BadMethodCallException("Undefined method '$method'. The method name must be one of '$methods'");
    }

    /**
     * Set container of FlashMessages.
     *
     * @param ComponentFlashMessages $container
     */
    public static function setContainer(ComponentFlashMessages $container)
    {
        self::$container = $container;
    }
}

class_alias('Lotgd\Core\Fixed\FlashMessages', 'LotgdFlashMessages', false);
