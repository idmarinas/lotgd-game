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

use Symfony\Contracts\Cache\CacheInterface;

/**
 * Return instance of cache of game "Cache\Core\Lotgd".
 */
trait Cache
{
    protected $lotgdCacheApp;

    /**
     * Get app cache instance.
     *
     * @return object
     */
    public function getCacheApp()
    {
        if ( ! $this->lotgdCacheApp instanceof CacheInterface)
        {
            $this->lotgdCacheApp = $this->getService('cache.app');
        }

        return $this->lotgdCacheApp;
    }

    /**
     * Get app cache instance.
     *
     * @return object
     */
    public function getCacheAppTag()
    {
        if ( ! $this->lotgdCacheApp instanceof CacheInterface)
        {
            $this->lotgdCacheApp = $this->getService('core.lotgd.cache');
        }

        return $this->lotgdCacheApp;
    }
}
