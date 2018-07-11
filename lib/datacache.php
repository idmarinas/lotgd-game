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
require_once 'lib/cache.php';

global $lotgd_cache;

$lotgd_cache = new LotgdCache();

/**
 * Get data cache.
 *
 * @param string $name     Key for a data storage
 * @param int    $duration Duration of the cache
 * @param bolean $force    Force to use cache
 *
 * @return mixed Data on success, null on failure
 */
function datacache($name, $duration = 120, $force = false)
{
    global $lotgd_cache, $DB_DATACACHEPATH, $DB_USEDATACACHE;

    $usedatacache = (bool) $DB_USEDATACACHE;

    if (false === $usedatacache && false === $force)
    {
        return false;
    }

    //-- Set Cache Dir
    $lotgd_cache->getOptions()->setCacheDir($DB_DATACACHEPATH);

    //-- Set Duration
    if (is_numeric($duration) && $duration > 0)
    {
        $lotgd_cache->getOptions()->setTtl($duration);
    }
    else
    {
        $lotgd_cache->getOptions()->setTtl(120);
    }

    return $lotgd_cache->getItem($name);
}

/**
 * Set data cache.
 *
 * @param string $name  Key for a data storage
 * @param mix    $data  Data to cache
 * @param bolean $force Force to update cache
 *
 * @return bool
 */
function updatedatacache($name, $data, $force = false)
{
    global $lotgd_cache, $DB_DATACACHEPATH, $DB_USEDATACACHE;

    $usedatacache = (bool) $DB_USEDATACACHE;

    if (false === $usedatacache && false === $force)
    {
        return false;
    }

    //-- Set Cache Dir
    $lotgd_cache->getOptions()->setCacheDir($DB_DATACACHEPATH);

    return $lotgd_cache->setItem($name, $data);
}

/**
 * We want to be able to invalidate data caches when we know we've done
 * something which would change the data.
 *
 * @param string $name  Key for a data storage
 * @param bolean $force Force to invalidate cache
 *
 * @return bool
 */
function invalidatedatacache($name, $force = false)
{
    global $lotgd_cache, $DB_DATACACHEPATH, $DB_USEDATACACHE;

    $usedatacache = (bool) $DB_USEDATACACHE;

    if (false === $usedatacache && false === $force)
    {
        return false;
    }

    //-- Set Cache Dir
    $lotgd_cache->getOptions()->setCacheDir($DB_DATACACHEPATH);

    return $lotgd_cache->removeItem($name);
}

/**
 * Invalidates *all* caches, which $prefix of their filename.
 *
 * @param string $prefix Prefix to invalidate
 * @param bolean $force  Force to remove cache
 *
 * @return bool
 */
function massinvalidate($prefix, $force = false)
{
    global $lotgd_cache, $DB_DATACACHEPATH, $DB_USEDATACACHE;

    $usedatacache = (bool) $DB_USEDATACACHE;

    if (false === $usedatacache && false === $force)
    {
        return false;
    }

    //-- Set Cache Dir
    $lotgd_cache->getOptions()->setCacheDir($DB_DATACACHEPATH);

    return $lotgd_cache->clearByPrefix($prefix);
}

/**
 * Flush the whole storage.
 *
 * @return bool
 */
function datacache_empty()
{
    global $lotgd_cache, $DB_DATACACHEPATH, $DB_USEDATACACHE;

    //-- Set Cache Dir
    $lotgd_cache->getOptions()->setCacheDir($DB_DATACACHEPATH);

    try
    {
        $result = $lotgd_cache->flush();
    }
    catch (\Exception $ex)
    {
        //-- With this avoid a 500 server error
        //-- In some cases it may not be possible to delete certain files and directories because it not have permissions.
        $result = true;
    }

    return $result;
}

/**
 * Remove expired data cache.
 *
 * @return bool
 */
function datacache_clearExpired()
{
    global $lotgd_cache, $DB_DATACACHEPATH, $DB_USEDATACACHE;

    //-- Set Cache Dir
    $lotgd_cache->getOptions()->setCacheDir($DB_DATACACHEPATH);

    return $lotgd_cache->clearExpired();
}

/**
 * Optimize the storage.
 *
 * @return bool
 */
function datacache_optimize()
{
    global $lotgd_cache, $DB_DATACACHEPATH, $DB_USEDATACACHE;

    //-- Set Cache Dir
    $lotgd_cache->getOptions()->setCacheDir($DB_DATACACHEPATH);

    return $lotgd_cache->optimize();
}
