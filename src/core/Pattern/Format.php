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

use Lotgd\Core\Output\Format as FormatCore;

@trigger_error(Format::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
trait Format
{
    protected $lotgdFormat;

    /**
     * Get format instance.
     *
     * @return object|null
     */
    public function getFormat()
    {
        if ( ! $this->lotgdFormat instanceof FormatCore)
        {
            $this->lotgdFormat = $this->getService(FormatCore::class);
        }

        return $this->lotgdFormat;
    }
}
