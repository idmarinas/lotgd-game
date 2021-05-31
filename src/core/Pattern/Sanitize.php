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

use Lotgd\Core\Tool\Sanitize as SanitizeCore;

@trigger_error(Sanitize::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
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
        if ( ! $this->lotgdSanitize instanceof SanitizeCore)
        {
            $this->lotgdSanitize = $this->getService(SanitizeCore::class);
        }

        return $this->lotgdSanitize;
    }
}
