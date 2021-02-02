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

use Lotgd\Core\Navigation\AccessKeys as CoreAccessKeys;
use Lotgd\Core\Navigation\Navigation as NavigationCore;

@trigger_error(Navigation::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait Navigation
{
    protected $lotgdNavigation;
    protected $lotgdAccesskeys;

    /**
     * Get navigation instance.
     *
     * @return object|null
     */
    public function getNavigation()
    {
        if ( ! $this->lotgdNavigation instanceof NavigationCore)
        {
            $this->lotgdNavigation = $this->getService(NavigationCore::class);
        }

        return $this->lotgdNavigation;
    }

    /**
     * Get Navigation instance.
     */
    public function getAccesskeys(): CoreAccessKeys
    {
        if ( ! $this->lotgdAccesskeys instanceof CoreAccessKeys)
        {
            $this->lotgdAccesskeys = $this->getService(CoreAccessKeys::class);
        }

        return $this->lotgdAccesskeys;
    }
}
