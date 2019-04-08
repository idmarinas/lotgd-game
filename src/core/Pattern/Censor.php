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

use Lotgd\Core\Output\Censor as CensorCore;

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
        if (! $this->censor instanceof CensorCore)
        {
            $this->censor = $this->getContainer(CensorCore::class);
        }

        return $this->censor;
    }
}
