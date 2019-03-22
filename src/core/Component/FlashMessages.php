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

use Zend\Session\Container;
use Zend\Session\ManagerInterface;
use Zend\Stdlib\SplQueue;

/**
 * Flash Messages - implement messages based on session.
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
     * Array of all types of messages.
     */
    const TYPES = [
        self::TYPE_DEFAULT,
        self::TYPE_SUCCESS,
        self::TYPE_WARNING,
        self::TYPE_ERROR,
        self::TYPE_INFO
    ];

    /**
     * Whether a message has been added during this request.
     *
     * @var bool
     */
    protected $messageAdded = false;

    /**
     * @var ManagerInterface
     */
    protected $session;

    /**
     * @var Container
     */
    protected $sessionContainer;

    /**
     * Messages of request.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Set the session manager.
     *
     * @param ManagerInterface $manager
     *
     * @return FlashMessages
     */
    public function setSessionManager(ManagerInterface $manager)
    {
        $this->session = $manager;

        return $this;
    }

    /**
     * Get the session manager.
     *
     * @return ManagerInterface
     */
    public function getSessionManager()
    {
        if (! $this->session instanceof ManagerInterface)
        {
            $this->setSessionManager(Container::getDefaultManager());
        }

        return $this->session;
    }

    /**
     * Get container for flash messages.
     *
     * @return Container
     */
    public function getContainer()
    {
        if ($this->sessionContainer instanceof Container)
        {
            return $this->sessionContainer;
        }

        $this->sessionContainer = new Container('FlashMessages', $this->getSessionManager());

        return $this->sessionContainer;
    }

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
        $container = $this->getContainer();
        $type = $type ?: self::TYPE_INFO;

        if (! $this->messageAdded)
        {
            $this->getMessagesFromContainer();
            $container->setExpirationHops(1);
        }

        if (! isset($container->{$type}) || ! $container->{$type} instanceof SplQueue)
        {
            $container->{$type} = new SplQueue();
        }

        $container->{$type}->push($message);

        $this->messageAdded = true;

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
     * Get all current messages.
     *
     * @param string|null $type
     *
     * @return array
     */
    public function getMessages($type = null): array
    {
        $container = $this->getContainer();

        if ($type && $this->hasMessages($type))
        {
            return $this->messages[$type]->toArray();
        }

        $alerts = [];

        foreach (self::TYPES as $type)
        {
            if (isset($container->{$type}))
            {
                $alerts[$type] = $container->{$type}->toArray();
                unset($container->{$type});
            }
        }

        return $alerts;
    }

    /**
     * Whether a specific namespace has messages.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasMessages($type)
    {
        $this->getMessagesFromContainer();

        return isset($this->messages[$type]);
    }

    /**
     * Clear all messages from the container.
     *
     * @return bool True if messages were cleared, false if none existed
     */
    public function clearMessagesFromContainer()
    {
        $this->getMessagesFromContainer();

        if (empty($this->messages))
        {
            return false;
        }

        unset($this->messages);
        $this->messages = [];

        return true;
    }

    /**
     * Pull messages from the session container.
     *
     * Iterates through the session container, removing messages into the local scope.
     */
    protected function getMessagesFromContainer()
    {
        if (! empty($this->messages) || $this->messageAdded)
        {
            return;
        }

        $container = $this->getContainer();

        $namespaces = [];

        foreach ($container as $type => $messages)
        {
            $this->messages[$type] = $messages;
            $namespaces[] = $type;
        }

        foreach ($namespaces as $type)
        {
            unset($container->{$type});
        }
    }
}
