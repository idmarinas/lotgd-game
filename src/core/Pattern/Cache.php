<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

use Laminas\Cache\Storage\StorageInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Return instance of cache of game "Cache\Core\Lotgd".
 */
trait Cache
{
    protected $lotgdCache;
    protected $lotgdCacheApp;

    /**
     * Get cache instance.
     *
     * @deprecated 4.9.0 use getCacheApp() instead
     *
     * @return object
     */
    public function getCache()
    {
        if ( ! $this->lotgdCache instanceof StorageInterface)
        {
            $this->lotgdCache = $this->getContainer('Cache\Core\Lotgd');
        }

        return $this->lotgdCache;
    }

    /**
     * Get app cache instance.
     *
     * @return object
     */
    public function getCacheApp()
    {
        if ( ! $this->lotgdCacheApp instanceof CacheInterface)
        {
            $this->lotgdCacheApp = \LotgdKernel::get('cache.app');
        }

        return $this->lotgdCacheApp;
    }
}
