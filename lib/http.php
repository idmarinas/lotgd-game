<?php

// translator ready
// addnews ready
// mail ready

use Zend\Http\PhpEnvironment\Request;

global $lotgd_request;

$lotgd_request = new Request();

/**
 * Return single get parameter.
 *
 * @param string $name
 * @param mixed  $default
 *
 * @return mixed
 */
function httpget($name, $default = null)
{
    global $lotgd_request;

    return $lotgd_request->getQuery($name, $default);
}

/**
 * Return all get parameters.
 *
 * @param bool $array For get or not data in array format
 *
 * @return array|object
 */
function httpallget($array = true)
{
    global $lotgd_request;

    if ($array)
    {
        return $lotgd_request->getQuery()->toArray();
    }
    else
    {
        return $lotgd_request->getQuery();
    }
}

/**
 * Set single get parameter.
 *
 * @param string $var
 * @param mixed  $val
 * @param bool   $force
 */
function httpset($var, $val, $force = false)
{
    global $lotgd_request;

    $get = $lotgd_request->getQuery();

    if ($get->offsetExists($var) || $force)
    {
        $get->set($var, $val);
    }
}

/**
 * Return single post parameter.
 *
 * @param string $name
 * @param mixed  $default
 *
 * @return mixed
 */
function httppost($name, $default = null)
{
    global $lotgd_request;

    return $lotgd_request->getPost($name, $default);
}

function httppostisset($var)
{
    global $lotgd_request;

    return $lotgd_request->getPost()->offsetExists($var);
}

/**
 * Set single post parameter.
 *
 * @param string $var
 * @param mixed  $val
 * @param bool   $sub
 */
function httppostset($var, $val, $sub = false)
{
    global $lotgd_request;

    $post = $lotgd_request->getPost();

    if (false === $sub)
    {
        if ($post->offsetExists($var))
        {
            $post->set($var, $val);
        }
    }
    else
    {
        if (isset($_POST[$var]) && isset($_POST[$var][$sub]))
        {
            $_POST[$var][$sub] = $val;

            $lotgd_request->setPost($_POST);
        }
    }
}

/**
 * Get all post data.
 *
 * @param bool $array For get or not data in array format
 *
 * @return array|object
 */
function httpallpost($array = true)
{
    global $lotgd_request;

    if ($array)
    {
        return $lotgd_request->getPost()->toArray();
    }
    else
    {
        return $lotgd_request->getPost();
    }
}

function postparse($verify = false, $subval = false)
{
    if ($subval)
    {
        $var = httppost($subval);
    }
    else
    {
        $var = httpallpost();
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
