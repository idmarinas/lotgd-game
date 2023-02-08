<?php

require_once __DIR__.'/functions_old.php';

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
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
            return \mt_rand();
        }
        $min = \round($min);

        if ( ! \is_numeric($max))
        {
            return $min;
        }
        $max = \round($max);

        return \mt_rand(\min($min, $max), \max($min, $max));
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
            return \mt_rand();
        }
        $min *= 1000;

        if ( ! \is_numeric($max))
        {
            return \mt_rand($min) / 1000;
        }
        $max *= 1000;

        return \mt_rand(\min($min, $max), \max($min, $max)) / 1000;
    }
}

