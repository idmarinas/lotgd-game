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

use Symfony\Component\HttpFoundation\Session\Flash\FlashBag as ComponentFlashMessages;

class FlashMessages
{
    /**
     * Instance of FlashMessages.
     *
     * @var object
     */
    protected static $instance;

    /**
     * Add support for magic static method calls.
     *
     * @param string $name
     * @param array  $arguments
     * @param mixed  $method
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
     * Add message.
     *
     * @param array|string $message
     * @param string       $type
     */
    public static function addMessage($message, $type = null)
    {
        $type = $type ?: 'info';

        self::$instance->add($type, $message);
    }

    /**
     * Add a "info" message.
     *
     * @param array|string $message
     *
     * @return FlashMessages
     */
    public static function addInfoMessage($message)
    {
        self::$instance->add('info', $message);
    }

    /**
     * Add a "success" message.
     *
     * @param array|string $message
     *
     * @return FlashMessages
     */
    public static function addSuccessMessage($message)
    {
        self::$instance->add('success', $message);
    }

    /**
     * Add a "error" message.
     *
     * @param array|string $message
     *
     * @return FlashMessages
     */
    public static function addErrorMessage($message)
    {
        self::$instance->add('error', $message);
    }

    /**
     * Add a "warning" message.
     *
     * @param array|string $message
     *
     * @return FlashMessages
     */
    public static function addWarningMessage($message)
    {
        self::$instance->add('warning', $message);
    }

    /**
     * Set container of FlashMessages.
     */
    public static function instance(ComponentFlashMessages $instance)
    {
        self::$instance = $instance;
    }
}

\class_alias('Lotgd\Core\Fixed\FlashMessages', 'LotgdFlashMessages', false);
