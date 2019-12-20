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

use Lotgd\Core\Navigation\Navigation as NavigationCore;

trait Navigation
{
    protected $lotgdNavigation;

    /**
     * Get navigation instance.
     *
     * @return object|null
     */
    public function getNavigation()
    {
        if (! $this->lotgdNavigation instanceof NavigationCore)
        {
            $this->lotgdNavigation = $this->getContainer(NavigationCore::class);
        }

        return $this->lotgdNavigation;
    }
}
