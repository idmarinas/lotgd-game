<?php

// translator ready
// addnews ready
// mail ready
function httpget($var)
{
    return isset($_GET[$var]) ? $_GET[$var] : false;
}

function httpallget()
{
    return $_GET;
}

function httpset($var, $val, $force = false)
{
    if (isset($_GET[$var]) || $force)
    {
        $_GET[$var] = $val;
    }
}

function httppost($var)
{
    return isset($_POST[$var]) ? $_POST[$var] : false;
}

function httppostisset($var)
{
    return (bool) isset($_POST[$var]) ? 1 : 0;
}

function httppostset($var, $val, $sub = false)
{
    if (false === $sub)
    {
        if (isset($_POST[$var]))
        {
            $_POST[$var] = $val;
        }
    }
    else
    {
        if (isset($_POST[$var]) && isset($_POST[$var][$sub]))
        {
            $_POST[$var][$sub] = $val;
        }
    }
}

function httpallpost()
{
    return $_POST;
}

function postparse($verify = false, $subval = false)
{
    if ($subval)
    {
        $var = $_POST[$subval];
    }
    else
    {
        $var = $_POST;
    }

    reset($var);
    $sql = '';
    $keys = '';
    $vals = '';
    $i = 0;

    foreach ($var as $key => $val)
    {
        if (false === $verify || isset($verify[$key]))
        {
            if (is_array($val))
            {
                $val = addslashes(serialize($val));
            }
            $sql .= (($i > 0) ? ',' : '')."$key='$val'";
            $keys .= (($i > 0) ? ',' : '')."$key";
            $vals .= (($i > 0) ? ',' : '')."'$val'";
            $i++;
        }
    }

    return [$sql, $keys, $vals];
}

/**
 * Return base url of game.
 *
 * @param false|string $file
 *
 * @return string
 */
function lotgd_base_url($file = false)
{
    $basename = (! $file ? basename($_SERVER['SCRIPT_NAME']) : $file);

    if ($basename)
    {
        $path = ($_SERVER['PHP_SELF'] ? trim($_SERVER['PHP_SELF'], '/') : '');
        $basePos = strpos($path, $basename) ?: 0;
        $baseUrl = substr($path, 0, $basePos);
    }

    return  $baseUrl;
}

/**
 * Deprecated.
 */
function baseUrl($file = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 2.1.0; and delete in version 3.0.0 please use "%s" instead',
        __METHOD__,
        'lotgd_base_url'
    ), E_USER_DEPRECATED);

    return lotgd_base_url($file);
}
