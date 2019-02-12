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
     * @param string $string
     *
     * @return string
     */
    public function colorize(string $string): string
    {
        return appoencode($string, true);
    }

    /**
     * Uncolorize a string.
     *
     * @param string $string
     *
     * @return string
     */
    public function uncolorize(string $string): string
    {
        return color_sanitize($string);
    }

    /**
     * nltoappon a string.
     *
     * @param string $string
     *
     * @return string
     */
    public function nltoappon(string $string): string
    {
        require_once 'lib/nltoappon.php';

        return nltoappon($string);
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
     * @param mixed $string
     *
     * @return string
     */
    public function relativedate($string)
    {
        return \LotgdFormat::relativedate($string);
    }

    /**
     * Similar to filter "format" but second argument is an array
     *
     * @param string $string
     * @param array $arguments
     *
     * @return string
     */
    public function sprintf($string, array $arguments)
    {
        \array_unshift($arguments, $string);

        return call_user_func_array('sprintf', $arguments);
    }
}
