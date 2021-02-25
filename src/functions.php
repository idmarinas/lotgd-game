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
            return $min;
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

if ( ! \function_exists('arraytourl'))
{
    /**
     * Turns an array into an URL argument string.
     *
     * Takes an array and encodes it in key=val&key=val form.
     * Does not add starting ?
     *
     * @param array $array The array to turn into an URL
     *
     * @return string The URL
     */
    function arraytourl($array)
    {
        //takes an array and encodes it in key=val&key=val form.
        $url = '';
        $i   = 0;

        foreach ($array as $key => $val)
        {
            if ($i > 0)
            {
                $url .= '&';
            }
            ++$i;
            $url .= \rawurlencode($key).'='.\rawurlencode($val);
        }

        return $url;
    }
}

if ( ! \function_exists('urltoarray'))
{
    /**
     * Takes an array and returns its arguments in an array.
     *
     * @param string $url The URL
     *
     * @return array The arguments from the URL
     */
    function urltoarray($url)
    {
        //takes a URL and returns its arguments in array form.
        if (false !== \strpos($url, '?'))
        {
            $url = \substr($url, \strpos($url, '?') + 1);
        }
        $a     = \explode('&', $url);
        $array = [];

        foreach ($a as $val)
        {
            $b                        = \explode('=', $val);
            $array[\urldecode($b[0])] = \urldecode($b[1]);
        }

        return $array;
    }
}

if ( ! \function_exists('createstring'))
{
    /**
     * Turns the given parameter into a string.
     *
     * If the given parameter is an array or object,
     * it is serialized, and the serialized string is
     * return.
     *
     * Otherwise, the parameter is cast as a string
     * and returned.
     *
     * @param mixed $array
     *
     * @return string The parameter converted to a string
     */
    function createstring($array)
    {
        if (\is_array($array) || \is_object($array))
        {
            $out = \serialize($array);
        }
        else
        {
            $out = (string) $array;
        }

        return $out;
    }
}

if ( ! \function_exists('myDefine'))
{
    function myDefine($name, $value)
    {
        //-- No try to define a defined constant
        if ( ! \defined($name))
        {
            \define($name, $value);
        }
    }
}

if ( ! \function_exists('pullurl'))
{
    function pullurl($url)
    {
        //if (function_exists("curl_init")) return _curl($url);
        // For some reason the socket code isn't working
        //if (function_exists("fsockopen")) return _sock($url);
        return @\file($url);
    }
}

if ( ! \function_exists('safeescape'))
{
    function safeescape($input)
    {
        $prevchar = '';
        $out      = '';

        for ($x = 0; $x < \strlen($input); ++$x)
        {
            $char = \substr($input, $x, 1);

            if (("'" == $char || '"' == $char) && '\\' != $prevchar)
            {
                $char = "\\{$char}";
            }
            $out .= $char;
            $prevchar = $char;
        }

        return $out;
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
