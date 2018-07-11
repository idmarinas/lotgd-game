<?php

// addnews ready
// translator ready
// mail ready
function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());

    return (float) $sec + ((float) $usec * 100000);
}

function e_rand($min = false, $max = false)
{
    if (false === $min)
    {
        return @mt_rand();
    }
    $min = round($min);

    if (false === $max)
    {
        return @mt_rand($min);
    }
    $max = round($max);

    if ($min == $max)
    {
        return $min;
    }
    //do NOT ask me why the following line can be executed, it makes no sense,
    // but it *does* get executed.
    if (0 == $min && 0 == $max)
    {
        return 0;
    }

    if ($min < $max)
    {
        return @mt_rand($min, $max);
    }
    elseif ($min > $max)
    {
        return @mt_rand($max, $min);
    }
}

function r_rand($min = false, $max = false)
{
    if (false === $min)
    {
        return mt_rand();
    }
    $min *= 1000;

    if (false === $max)
    {
        return mt_rand($min) / 1000;
    }
    $max *= 1000;

    if ($min == $max)
    {
        return $min / 1000;
    }
    //do NOT ask me why the following line can be executed, it makes no sense,
    // but it *does* get executed.
    if (0 == $min && 0 == $max)
    {
        return 0;
    }

    if ($min < $max)
    {
        return @mt_rand($min, $max) / 1000;
    }
    elseif ($min > $max)
    {
        return @mt_rand($max, $min) / 1000;
    }
}
