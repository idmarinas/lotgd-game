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

use Lotgd\Core\Template\Theme as Environment;

/**
 * Trait to translator.
 */
trait Translator
{
    /**
     * Translate a string using Laminas Translator.
     *
     * @param string $message
     * @param array  $parameters
     * @param string $domain
     * @param string $locale
     */
    public function translate(Environment $env, $message, $parameters = [], $domain = null, $locale = null): string
    {
        if ( ! $message)
        {
            return '';
        }

        $nl2br = $env->getFilter('nl2br')->getCallable();

        return $nl2br($this->getTranslator()->trans($message, $parameters, $domain, $locale));
    }

    /**
     * Translate a string using Symfony Translator.
     *
     * @param string $message
     * @param array  $parameters
     * @param string $domain
     * @param string $locale
     */
    public function symfonyTrans(Environment $env, $message, $parameters = [], $domain = null, $locale = null): string
    {
        if ( ! $message)
        {
            return '';
        }

        $nl2br = $env->getFilter('nl2br')->getCallable();

        return $nl2br($this->symfonyTranslator()->trans($message, $parameters, $domain, $locale));
    }

    /**
     * Format a message with MessageFormatter.
     *
     * @param string $message
     * @param array  $parameters
     * @param string $locale
     */
    public function translateMf(Environment $env, $message, $parameters = [], $locale = null): string
    {
        if ( ! $message)
        {
            return '';
        }

        $nl2br = $env->getFilter('nl2br')->getCallable();

        return $nl2br($this->messageFormatter($message, $parameters, $locale));
    }

    /**
     * Format a message without MessageFormatter.
     *
     * @param string      $message
     * @param string|null $domain
     * @param string|null $locale
     */
    public function translateSt(Environment $env, $message, $domain = null, $locale = null): string
    {
        if ( ! $message)
        {
            return '';
        }

        $nl2br = $env->getFilter('nl2br')->getCallable();

        return $nl2br($this->getTranslator()->translate($message, $domain, $locale));
    }

    /**
     * Get locale for translator.
     */
    public function translatorDefaultLocale(): string
    {
        //-- Priorize locale of Symfony translator
        if ($this->symfonyTranslator()->getLocale() == $this->getTranslator()->getLocale())
        {
            return $this->symfonyTranslator()->getLocale();
        }

        //-- Warning in bdump if not are equal
        bdump(\sprintf(
            'Laminas i18m "%s" Symfony Translator "%s"',
            $this->getTranslator()->getLocale(),
            $this->symfonyTranslator()->getLocale()
        ), 'Locales of translator not equal');

        return $this->symfonyTranslator()->getLocale();
    }
}
