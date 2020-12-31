<?php

/*function support without the object call */

/**
 * Generates the appropriate output based on the LOGD coding system (ie: `b: Bold, `i: Italic).
 *
 * @deprecated use \LotgdResponse::pageDebug($text, $force) instead
 */
function debug($text, $force = false)
{
    \LotgdResponse::pageDebug($text, $force);
}

/**
 * Generates the appropriate output based on the LOGD coding system (ie: `b: Bold, `i: Italic).
 *
 * @param string $data The string to be output
 * @param bool   $priv Indicates if the passed string ($data) contains HTML
 *
 * @return string An output (HTML) formatted string
 *
 * @deprecated use LotgdFormat::colorize($data) instead
 */
function appoencode(string $data, $priv = false)
{
    \trigger_error(\sprintf(
        'Usage of %s is obsolete since 4.7.0; and delete in future version, use new "LotgdFormat::colorize($string)"',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdFormat::colorize($data);
}
