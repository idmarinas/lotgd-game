<?php

require_once __DIR__.'/functions_old.php';

/*
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.10.0
 */
if ( ! \function_exists('e_rand'))
{
    /**
     * Alias of mt_rand with some improvements.
     *
     * @param int|float $min
     * @param int|float $max
     */
    function e_rand($min = null, $max = null): int
    {
        if ( ! \is_numeric($min))
        {
            return \random_int(0, \mt_getrandmax());
        }
        $min = \round($min);

        if ( ! \is_numeric($max))
        {
            return \random_int($min, \mt_getrandmax());
        }
        $max = \round($max);

        return \random_int(\min($min, $max), \max($min, $max));
    }
}

if ( ! \function_exists('r_rand'))
{
    /**
     * Same as e_rand but $min and $max are multiplied by 1000.
     *
     * @param int|float $min
     * @param int|float $max
     *
     * @return int|float
     */
    function r_rand($min = null, $max = null)
    {
        if ( ! \is_numeric($min))
        {
            return \random_int(0, \mt_getrandmax());
        }
        $min *= 1000;

        if ( ! \is_numeric($max))
        {
            return \random_int($min, \mt_getrandmax()) / 1000;
        }
        $max *= 1000;

        return \random_int(\min($min, $max), \max($min, $max)) / 1000;
    }
}

if ( ! \function_exists('nltoappon'))
{
    function nltoappon($in)
    {
        $out = \str_replace("\r\n", "\n", $in);
        $out = \str_replace("\r", "\n", $out);

        return \str_replace("\n", '`n', $out);
    }
}
