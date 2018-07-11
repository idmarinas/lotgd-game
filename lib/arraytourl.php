<?php

// addnews ready
// translator ready
// mail ready
/**
 * URL <-> array functions.
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2009, Dragonprime Development Team
 *
 * @version Lotgd 1.1.2 DragonPrime Edition
 *
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
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
    $i = 0;

    foreach ($array as $key => $val)
    {
        if ($i > 0)
        {
            $url .= '&';
        }
        $i++;
        $url .= rawurlencode($key).'='.rawurlencode($val);
    }

    return $url;
}
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
    if (false !== strpos($url, '?'))
    {
        $url = substr($array, strpos($url, '?') + 1);
    }
    $a = explode('&', $url);
    $array = [];

    foreach ($a as $val)
    {
        $b = explode('=', $val);
        $array[urldecode($b[0])] = urldecode($b[1]);
    }

    return $array;
}
