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

\trigger_error(\sprintf(
    'Class %s is deprecated in 4.5.0 and deleted in 5.0.0, please use %s instead',
    Theme::class,
    Template::class
), E_USER_DEPRECATED);

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
        if ( ! $this->lotgdTheme instanceof ThemeCore)
        {
            $this->lotgdTheme = $this->getContainer(ThemeCore::class);
        }

        return $this->lotgdTheme;
    }
}
