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

namespace Lotgd\Core\Twig\Extension\Pattern;

/**
 * Trait to translator.
 */
trait Translator
{
    /**
     * Translate a string using translator.
     *
     * @param string $message
     * @param array  $parameters
     * @param string $domain
     * @param string $locale
     */
    public function translate($message, $parameters = [], $domain = null, $locale = null): string
    {
        if (! $message)
        {
            return '';
        }

        return $this->getTranslator()->trans($message, $parameters, $domain, $locale);
    }

    /**
     * Format a message with MessageFormatter.
     *
     * @param string $message
     * @param array  $parameters
     * @param string $locale
     */
    public function translateMf($message, $parameters = [], $locale = null): string
    {
        if (! $message)
        {
            return '';
        }

        return $this->getTranslator()->mf($message, $parameters, $locale);
    }

    /**
     * Format a message without MessageFormatter.
     *
     * @param string      $message
     * @param string|null $domain
     * @param string|null $locale
     */
    public function translateSt($message, $domain = null, $locale = null): string
    {
        if (! $message)
        {
            return '';
        }

        return $this->getTranslator()->translate($message, $domain, $locale);
    }

    /**
     * Get locale for translator.
     */
    public function translatorDefaultLocale(): string
    {
        return $this->getTranslator()->getLocale();
    }
}
