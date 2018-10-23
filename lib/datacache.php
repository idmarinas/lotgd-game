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
 * Reworked by IDMarinas.
 */
$lotgdCache = $lotgdServiceManager->get(Lotgd\Core\Lib\Cache::class);

/**
 * Get data cache.
 *
 * @param string $name     Key for a data storage
 * @param int    $duration Duration of the cache
 * @param bool   $force    Force to use cache
 *
 * @return mixed Data on success, null on failure
 */
function datacache(string $name, int $duration = 120, bool $force = false)
{
    global $lotgdCache;

    return $lotgdCache->getData($name, $duration, $force);
}

/**
 * Set data cache.
 *
 * @param string $name  Key for a data storage
 * @param mixed  $data  Data to cache
 * @param bool   $force Force to update cache
 *
 * @return bool
 */
function updatedatacache(string $name, $data, bool $force = false)
{
    global $lotgdCache;

    return $lotgdCache->updateData($name, $data, $force);
}

/**
 * We want to be able to invalidate data caches when we know we've done
 * something which would change the data.
 *
 * @param string $name  Key for a data storage
 * @param bool   $force Force to invalidate cache
 *
 * @return bool
 */
function invalidatedatacache($name, $force = false)
{
    global $lotgdCache;

    return $lotgdCache->invalidateData($name, $force);
}

/**
 * Invalidates *all* caches, which $prefix of their filename.
 *
 * @param string $prefix Prefix to invalidate
 * @param bool   $force  Force to remove cache
 *
 * @return bool
 */
function massinvalidate($prefix, $force = false)
{
    global $lotgdCache;

    return $lotgdCache->massInvalidate($prefix);
}

/**
 * Flush the whole storage.
 *
 * @return bool
 */
function datacache_empty()
{
    global $lotgdCache;

    return $lotgdCache->dataEmpty();
}

/**
 * Remove expired data cache.
 *
 * @return bool
 */
function datacache_clearExpired()
{
    global $lotgdCache;

    return $lotgdCache->dataClearExpired();
}

/**
 * Optimize the storage.
 *
 * @return bool
 */
function datacache_optimize()
{
    global $lotgdCache;

    return $lotgdCache->dataOptimize();
}
