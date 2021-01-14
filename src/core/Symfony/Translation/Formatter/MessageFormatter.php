<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.11.0
 */

namespace Lotgd\Core\Symfony\Translation\Formatter;

use Symfony\Component\Translation\Formatter\IntlFormatterInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter as CoreMessageFormatter;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;

class MessageFormatter extends CoreMessageFormatter implements MessageFormatterInterface, IntlFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function formatIntl(string $message, string $locale, array $parameters = []): string
    {
        //-- Delete all values that not are allowed (can cause a error when use \MessageFormatter::format($params))
        $parameters = \array_filter($parameters, [$this, 'cleanParameters']);

        return parent::formatIntl($message, $locale, $parameters);
    }

    /**
     * Clean param of a value.
     *
     * @param mixed $param
     */
    private function cleanParameters($param): bool
    {
        return (bool) (
            \is_string($param) //-- Allow string values
            || \is_numeric($param) //-- Allow numeric values
            || \is_bool($param) //-- Allow bool values
            || \is_null($param) //-- Allow null value (Formatter can handle this value)
            || $param instanceof \DateTime //-- Allow DateTime object
        );
    }
}
