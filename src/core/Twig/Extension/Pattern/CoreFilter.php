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

trait CoreFilter
{
    /**
     * Colorize a string.
     *
     * @param string|null $string
     *
     * @return string
     */
    public function colorize(?string $string): string
    {
        if (! $string)
        {
            return '';
        }

        return appoencode($string, true);
    }

    /**
     * Uncolorize a string.
     * Remove all LoTGD codes.
     *
     * @param string|null $string
     *
     * @return string
     */
    public function uncolorize(?string $string): string
    {
        if (! $string)
        {
            return '';
        }

        return \LotgdSanitize::fullSanitize($string);
    }

    /**
     * Preven to format a LotGD code.
     *
     * @param string|null $string
     *
     * @return string
     */
    public function preventCodes(?string $string): string
    {
        if (! $string)
        {
            return '';
        }

        return \LotgdSanitize::preventLotgdCodes($string);
    }

    /**
     * Add a link, but not nav.
     *
     * @param string $string
     *
     * @return string
     */
    public function lotgdUrl(string $link): string
    {
        \LotgdNavigation::addNavAllow($link);

        return $link;
    }

    /**
     * Format a number.
     *
     * @param int|float $nuemral
     * @param int|null  $decimals
     *
     * @return string
     */
    public function numeral($number, ?int $decimals = 0)
    {
        return \LotgdFormat::numeral($number, $decimals);
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
        return \LotgdFormat::relativedate($string, $default);
    }

    /**
     * Similar to filter "format" but second argument is an array.
     *
     * @param string $string
     * @param array  $arguments
     *
     * @return string
     */
    public function sprintfnews($string, array $arguments)
    {
        if (is_array($arguments[0]) && is_array($arguments[1]))
        {
            array_shift($arguments[0]);
            $domain1 = array_shift($arguments[0]);
            $text1 = array_shift($arguments[0]);

            array_shift($arguments[1]);
            $domain2 = array_shift($arguments[1]);
            $text2 = array_shift($arguments[1]);

            $arg1 = vsprintf(\LotgdTranslator::t($text1, [], $domain1), $arguments[0]);
            $arg2 = vsprintf(\LotgdTranslator::t($text2, [], $domain2), $arguments[1]);

            return sprintf($string, $arg1, $arg2);
        }

        \array_unshift($arguments, $string);

        return call_user_func_array('sprintf', $arguments);
    }

    /**
     * Filter string with censor.
     *
     * @param string|null $string
     *
     * @return string
     */
    public function censor(?string $string): string
    {
        $censor = $this->getCensor();

        return $censor->filter($string);
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
        return highlight_file($file, true);
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
        return highlight_string("<?php \n\r".$string, true);
    }
}
