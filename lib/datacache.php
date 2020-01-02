<?php

// translator ready
// addnews ready
// mail ready
//This is a data caching library intended to lighten the load on lotgd.net
//use of this library is not recommended for most installations as it raises
//the issue of some race conditions which are mitigated on high volume
//sites but which could cause odd behavior on low volume sites, with out
//offering much if any advantage.

//basically the idea behind this library is to provide a non-blocking
//storage mechanism for non-critical data.

/**
 * @deprecated 4.1.0
 */

/**
 * Get data cache.
 *
 * @param string $name     Key for a data storage
 * @param int    $duration Duration of the cache
 * @param bool   $force    Force to use cache
 *
 * @deprecated 4.1.0
 *
 * @return mixed Data on success, null on failure
 */
function datacache(string $name, int $duration = 120, bool $force = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.1.0; and delete in version 4.2.0, use new "LotgdCache::getItem($string)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdCache::getItem($name);
}

/**
 * Set data cache.
 *
 * @param string $name  Key for a data storage
 * @param mixed  $data  Data to cache
 * @param bool   $force Force to update cache
 *
 * @deprecated 4.1.0
 *
 * @return bool
 */
function updatedatacache(string $name, $data, bool $force = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.1.0; and delete in version 4.2.0, use new "LotgdCache::setItem($string, $data)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);


    return \LotgdCache::setItem($name, $data);
}

/**
 * We want to be able to invalidate data caches when we know we've done
 * something which would change the data.
 *
 * @param string $name  Key for a data storage
 * @param bool   $force Force to invalidate cache
 *
 * @deprecated 4.1.0
 *
 * @return bool
 */
function invalidatedatacache($name, $force = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.1.0; and delete in version 4.2.0, use new "LotgdCache::removeItem($string)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdCache::removeItem($name);
}

/**
 * Invalidates *all* caches, which $prefix of their filename.
 *
 * @param string $prefix Prefix to invalidate
 * @param bool   $force  Force to remove cache
 *
 * @deprecated 4.1.0
 *
 * @return bool
 */
function massinvalidate($prefix)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.1.0; and delete in version 4.2.0, use new "LotgdCache::clearByPrefix($string)" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdCache::clearByPrefix($prefix);
}

/**
 * Flush the whole storage.
 *
 * @deprecated 4.1.0
 *
 * @return bool
 */
function datacache_empty()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.1.0; and delete in version 4.2.0, use new "LotgdCache::flush()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdCache::flush();
}

/**
 * Remove expired data cache.
 *
 * @deprecated 4.1.0
 *
 * @return bool
 */
function datacache_clearExpired()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.1.0; and delete in version 4.2.0, use new "LotgdCache::clearExpired()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdCache::clearExpired();
}

/**
 * Optimize the storage.
 *
 * @deprecated 4.1.0
 *
 * @return bool
 */
function datacache_optimize()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.1.0; and delete in version 4.2.0, use new "LotgdCache::optimize()" instead.',
        __METHOD__
    ), E_USER_DEPRECATED);

    return \LotgdCache::optimize();
}
