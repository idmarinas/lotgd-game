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

namespace Lotgd\Core\Twig\Extension\Pattern;

trait CoreFilter
{
    /**
     * Colorize a string.
     */
    public function colorize(?string $string): string
    {
        if ( ! $string)
        {
            return '';
        }

        return $this->format->colorize($string, true);
    }

    /**
     * Preven to format a LotGD code.
     */
    public function preventCodes(?string $string): string
    {
        if ( ! $string)
        {
            return '';
        }

        return $this->sanitize->preventLotgdCodes($string);
    }

    /**
     * Format a number.
     *
     * @param int|float $nuemral
     * @param mixed     $number
     *
     * @return string
     */
    public function numeral($number, ?int $decimals = 0)
    {
        return $this->format->numeral($number, (int) $decimals);
    }

    /**
     * Show a relative date from now.
     *
     * @param mixed  $string
     * @param string $default
     *
     * @return string
     */
    public function relativedate($string, $default = null)
    {
        return $this->format->relativedate($string, $default);
    }

    /**
     * Similar to filter "format" but second argument is an array.
     *
     * @param string $string
     *
     * @return string
     */
    public function sprintfnews($string, array $arguments)
    {
        if (\is_array($arguments[0]) && \is_array($arguments[1]))
        {
            \array_shift($arguments[0]);
            $domain1 = \array_shift($arguments[0]);
            $text1   = \array_shift($arguments[0]);

            \array_shift($arguments[1]);
            $domain2 = \array_shift($arguments[1]);
            $text2   = \array_shift($arguments[1]);

            $arg1 = \vsprintf($this->translator->trans($text1, [], $domain1), $arguments[0]);
            $arg2 = \vsprintf($this->translator->trans($text2, [], $domain2), $arguments[1]);

            return \sprintf($string, $arg1, $arg2);
        }

        \array_unshift($arguments, $string);

        return \call_user_func_array('sprintf', $arguments);
    }

    /**
     * Syntax highlighting of a file.
     *
     * @param string $file
     *
     * @return string|null
     */
    public function highlightFile($file)
    {
        return \highlight_file($file, true);
    }

    /**
     * Syntax highlighting of a string.
     *
     * @param string $string
     *
     * @return string|null
     */
    public function highlightString($string)
    {
        return \highlight_string("<?php \n\r".$string, true);
    }

    /**
     * Show an affirmation or negation.
     *
     * @param int|bool $value      Value to check
     * @param string   $yes        Translation key
     * @param string   $no         Translation key
     * @param string   $textDomain Domain for translation
     *
     * @return text
     */
    public function affirmationNegation($value, $yes = 'adverb.yes', $no = 'adverb.no', $textDomain = 'app_common')
    {
        $value = (int) $value;

        $text = 0 == $value ? $no : $yes;

        return $this->translator->trans($text, [], $textDomain);
    }
}
