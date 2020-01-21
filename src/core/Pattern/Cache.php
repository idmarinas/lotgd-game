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

use Zend\Cache\Storage\StorageInterface;

/**
 * Return instance of cache of game "Cache\Core\Lotgd".
 */
trait Cache
{
    protected $lotgdCache;

    /**
     * Get cache instance.
     *
     * @return object|null
     */
    public function getCache()
    {
        if (! $this->lotgdCache instanceof StorageInterface)
        {
            $this->lotgdCache = $this->getContainer('Cache\Core\Lotgd');
        }

        return $this->lotgdCache;
    }
}
