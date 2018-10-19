<?php

// translator ready
// addnews ready
// mail ready

/**
 * Return single get parameter.
 *
 * @param string $name
 * @param string  $default
 *
 * @return mixed
 */
function httpget(string $name, string $default = null)
{
    global $lotgdServiceManager;

    return $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getQuery($name, $default);
}

/**
 * Return all get parameters.
 *
 * @param bool $array For get or not data in array format
 *
 * @return array|object
 */
function httpallget(bool $array = true)
{
    global $lotgdServiceManager;

    if ($array)
    {
        return $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getQuery()->toArray();
    }
    else
    {
        return $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getQuery();
    }
}

/**
 * Set single get parameter.
 *
 * @param string $var
 * @param mixed  $val
 * @param bool   $force
 */
function httpset(string $var, $val, bool $force = false)
{
    global $lotgdServiceManager;

    $get = $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getQuery();

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
    global $lotgdServiceManager;

    return $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getPost($name, $default);
}

function httppostisset($var)
{
    global $lotgdServiceManager;

    return $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getPost()->offsetExists($var);
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
    global $lotgdServiceManager;

    $post = $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getPost();

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

            $lotgdServiceManager->get(\Lotgd\Core\Http::class)->setPost($_POST);
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
    global $lotgdServiceManager;

    if ($array)
    {
        return $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getPost()->toArray();
    }
    else
    {
        return $lotgdServiceManager->get(\Lotgd\Core\Http::class)->getPost();
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
