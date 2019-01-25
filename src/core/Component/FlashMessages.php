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

namespace Lotgd\Core\Component;

/**
 * Flash Messages - implement messages based on sension.
 */
class FlashMessages
{
    /**
     * Default type messages.
     */
    const TYPE_DEFAULT = 'default';

    /**
     * Success type messages.
     */
    const TYPE_SUCCESS = 'success';

    /**
     * Warning type messages.
     */
    const TYPE_WARNING = 'warning';

    /**
     * Error type messages.
     */
    const TYPE_ERROR = 'error';

    /**
     * Info type messages.
     */
    const TYPE_INFO = 'info';

    /**
     * Messages of request.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Add message.
     *
     * @param string $message
     * @param string $type
     *
     * @return FlashMessages
     */
    public function addMessage(string $message, $type = null)
    {
        $type = $type ?: self::TYPE_INFO;

        $this->messages[$type][] = $message;

        return $this;
    }

    /**
     * Add a "info" message.
     *
     * @param string $message
     *
     * @return FlashMessages
     */
    public function addInfoMessage(string $message)
    {
        $this->addMessage($message, self::TYPE_INFO);

        return $this;
    }

    /**
     * Add a "success" message.
     *
     * @param string $message
     *
     * @return FlashMessages
     */
    public function addSuccessMessage(string $message)
    {
        $this->addMessage($message, self::TYPE_SUCCESS);

        return $this;
    }

    /**
     * Add a "error" message.
     *
     * @param string $message
     *
     * @return FlashMessages
     */
    public function addErrorMessage(string $message)
    {
        $this->addMessage($message, self::TYPE_ERROR);

        return $this;
    }

    /**
     * Add a "warning" message.
     *
     * @param string $message
     *
     * @return FlashMessages
     */
    public function addWarningMessage(string $message)
    {
        $this->addMessage($message, self::TYPE_WARNING);

        return $this;
    }

    /**
     * Get all messages.
     *
     * @param string $type
     *
     * @return array
     */
    public function getMessages($type = null): array
    {
        if (isset($this->messages[$type]))
        {
            return $this->messages[$type];
        }

        return $this->messages;
    }
}
