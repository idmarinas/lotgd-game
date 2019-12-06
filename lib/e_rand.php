<?php

// addnews ready
// translator ready
// mail ready
function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());

    return (float) $sec + ((float) $usec * 100000);
}

/**
 * Alias of mt_rand with some improvements.
 *
 * @param int|float $min
 * @param int|float $max
 *
 * @return int
 */
function e_rand($min = null, $max = null): int
{
    if (! is_numeric($min))
    {
        return mt_rand();
    }
    $min = round($min);

    if (! is_numeric($max))
    {
        return mt_rand($min);
    }
    $max = round($max);

    return mt_rand(min($min, $max), max($min, $max));
}

/**
 * Same as e_rand but $min and $max are multiplied by 1000
 *
 * @param int|float $min
 * @param int|float $max
 *
 * @return int|float
 */
function r_rand($min = null, $max = null)
{
    if (! is_numeric($min))
    {
        return mt_rand();
    }
    $min *= 1000;

    if (! is_numeric($max))
    {
        return mt_rand($min) / 1000;
    }
    $max *= 1000;

    return mt_rand(min($min, $max), max($min, $max)) / 1000;
}
