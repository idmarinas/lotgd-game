<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Fixed;

use function class_alias;

class FlashMessages
{
    use StaticTrait;

    /**
     * Add message.
     *
     * @param array|string $message
     * @param string       $type
     */
    public static function addMessage($message, string $type = null): void
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
    public static function addInfoMessage($message): void
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
    public static function addSuccessMessage($message): void
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
    public static function addErrorMessage($message): void
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
    public static function addWarningMessage($message): void
    {
        self::$instance->add('warning', $message);
    }
}

class_alias('Lotgd\Core\Fixed\FlashMessages', 'LotgdFlashMessages', false);
