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

use Lotgd\Core\Output\Censor as CensorCore;

@trigger_error(Censor::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait Censor
{
    protected $censor;

    /**
     * Get repository.
     *
     * @return object|null
     */
    public function getCensor()
    {
        if ( ! $this->censor instanceof CensorCore)
        {
            $this->censor = $this->getService(CensorCore::class);
        }

        return $this->censor;
    }
}
