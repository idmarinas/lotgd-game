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

use Lotgd\Core\Template\Theme as ThemeCore;

trait Theme
{
    protected $lotgdTheme;

    /**
     * Get theme instance.
     *
     * @return object|null
     */
    public function getTheme()
    {
        if (! $this->lotgdTheme instanceof ThemeCore)
        {
            $this->lotgdTheme = $this->getContainer(ThemeCore::class);
        }

        return $this->lotgdTheme;
    }
}
