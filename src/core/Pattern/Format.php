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
        if (! $this->lotgdFormat instanceof FormatCore)
        {
            $this->lotgdFormat = $this->getContainer(FormatCore::class);
        }

        return $this->lotgdFormat;
    }
}
