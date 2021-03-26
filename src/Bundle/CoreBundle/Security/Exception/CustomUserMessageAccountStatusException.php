<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lotgd\Bundle\CoreBundle\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Is a class of Symfony Framework 5.x
 */
class CustomUserMessageAccountStatusException extends AccountStatusException
{
    private $messageKey;

    private $messageData = [];

    public function __construct(string $message = '', array $messageData = [], int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->setSafeMessage($message, $messageData);
    }

    /**
     * Sets a message that will be shown to the user.
     *
     * @param string $messageKey  The message or message key
     * @param array  $messageData Data to be passed into the translator
     */
    public function setSafeMessage(string $messageKey, array $messageData = [])
    {
        $this->messageKey = $messageKey;
        $this->messageData = $messageData;
    }

    public function getMessageKey()
    {
        return $this->messageKey;
    }

    public function getMessageData()
    {
        return $this->messageData;
    }

    /**
     * {@inheritdoc}
     */
    public function __serialize(): array
    {
        return [parent::__serialize(), $this->messageKey, $this->messageData];
    }

    /**
     * {@inheritdoc}
     */
    public function __unserialize(array $data): void
    {
        [$parentData, $this->messageKey, $this->messageData] = $data;
        parent::__unserialize($parentData);
    }
}
