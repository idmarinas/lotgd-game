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

use Tracy\Debugger;
use Zend\Escaper\Exception\RuntimeException as EscaperException;
use Zend\View\Helper\EscapeHtml;
use Zend\View\Helper\EscapeHtmlAttr;

/**
 * Trait to translator.
 */
trait Translator
{
    /**
     * Translate a string using translator.
     *
     * @param string $message
     * @param array  $arguments
     * @param string $domain
     * @param string $locale
     *
     * @return string
     */
    public function translate($message, $parameters = [], $domain = null, $locale = null): string
    {
        return $this->getTranslator()->trans($message, $parameters, $domain, $locale);
    }

    /**
     * Format a message with MessageFormatter.
     *
     * @param string $message
     * @param array  $parameters
     * @param string $locale
     *
     * @return string
     */
    public function translateMf($message, $parameters = [], $locale = null): string
    {
        return $this->getTranslator()->mf($message, $parameters, $locale);
    }
}
