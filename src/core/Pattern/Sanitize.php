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

use Lotgd\Core\Tool\Sanitize as SanitizeCore;

trait Sanitize
{
    protected $lotgdSanitize;

    /**
     * Get sanitize instance.
     *
     * @return object|null
     */
    public function getSanitize()
    {
        if (! $this->lotgdSanitize instanceof SanitizeCore)
        {
            $this->lotgdSanitize = $this->getContainer(SanitizeCore::class);
        }

        return $this->lotgdSanitize;
    }
}
