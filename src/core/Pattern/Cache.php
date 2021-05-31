<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pattern;

use Symfony\Contracts\Cache\CacheInterface;

@trigger_error(Cache::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * Return instance of cache of game "Cache\Core\Lotgd".
 *
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
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
