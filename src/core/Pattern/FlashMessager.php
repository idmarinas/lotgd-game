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

use Lotgd\Core\Component\FlashMessages as FlashMessagerCore;

trait FlashMessager
{
    protected $lotgdFlashMessager;

    /**
     * Get FlashMessager instance.
     *
     * @return object|null
     */
    public function getFlashMessager()
    {
        if (! $this->lotgdFlashMessager instanceof FlashMessagerCore)
        {
            $this->lotgdFlashMessager = $this->getContainer(FlashMessagerCore::class);
        }

        return $this->lotgdFlashMessager;
    }
}
