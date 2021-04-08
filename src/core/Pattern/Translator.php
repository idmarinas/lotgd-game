<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

use Symfony\Contracts\Translation\TranslatorInterface;

@trigger_error(Translator::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait Translator
{
    protected $lotgdSymfonyTranslator;

    /**
     * Alias
     */
    public function getTranslator()
    {
        return $this->symfonyTranslator();
    }

    /**
     * Symfony Translator instance.
     */
    public function symfonyTranslator(): TranslatorInterface
    {
        if ( ! $this->lotgdSymfonyTranslator instanceof TranslatorInterface)
        {
            $this->lotgdSymfonyTranslator = $this->getService('translator');
        }

        return $this->lotgdSymfonyTranslator;
    }

    /**
     * Only format a message with MessageFormatter.
     */
    public function messageFormatter(string $message, ?array $parameters = [], ?string $locale = null): string
    {
        //-- Not do nothing if message is empty
        //-- MessageFormatter fail if message is empty
        if ('' == $message)
        {
            return '';
        }

        $locale     = ($locale ?: $this->symfonyTranslator()->getLocale());
        $parameters = ($parameters ?: []);
        //-- Delete all values that not are allowed (can cause a error when use \MessageFormatter::format($params))
        $parameters = \array_filter($parameters, [$this, 'cleanParameters']);

        $formatter = new \MessageFormatter($locale, $message);

        $msg = $formatter->format($parameters);

        //-- Dump error to debug
        if ($formatter->getErrorCode())
        {
            bdump($formatter->getPattern());
            bdump($formatter->getErrorMessage());
        }

        return $msg;
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
