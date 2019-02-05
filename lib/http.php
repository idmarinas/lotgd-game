<?php

// translator ready
// addnews ready
// mail ready

/**
 * @see Lotgd\Core\Fixed\Http
 */
function httpget(string $name, string $default = null)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use "LotgdHttp::getQuery($name, $default)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdLocator::get(\Lotgd\Core\Http::class)->getQuery($name, $default);
}

/**
 * @see Lotgd\Core\Fixed\Http
 */
function httpallget()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use "LotgdHttp::getAllQuery()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdHttp::getAllQuery();
}

/**
 * @see Lotgd\Core\Fixed\Http
 */
function httpset(string $var, $val, bool $force = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use "LotgdHttp::setQuery($var, $val, $force)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdHttp::setQuery($var, $val, $force);
}

/**
 * @see Lotgd\Core\Fixed\Http
 */
function httppost($name, $default = null)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use "LotgdHttp::getPost($name, $default)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdHttp::getPost($name, $default);
}

/**
 * @see Lotgd\Core\Fixed\Http
 */
function httppostisset($var)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use "LotgdHttp::existInPost($var)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdHttp::existInPost($var);
}

/**
 * @see Lotgd\Core\Fixed\Http
 */
function httppostset($var, $val, $sub = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use "LotgdHttp::setPost($var, $val, $sub)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdHttp::setPost($var, $val, $sub);
}

/**
 * @see Lotgd\Core\Fixed\Http
 */
function httpallpost()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use "LotgdHttp::getPost()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdHttp::getPost();
}

function postparse($verify = false, $subval = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and deleted in future version.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $var = httpallpost();

    if ($subval)
    {
        $var = httppost($subval);
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
    $basename = (! $file ? basename(LotgdHttp::getServer('SCRIPT_NAME')) : $file);

    if ($basename)
    {
        $path = (LotgdHttp::getServer('PHP_SELF') ? trim(LotgdHttp::getServer('PHP_SELF'), '/') : '');
        $basePos = strpos($path, $basename) ?: 0;
        $baseUrl = substr($path, 0, $basePos);
    }

    return $baseUrl ?? '';
}
