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

use Lotgd\Core\Component\FlashMessages as FlashMessengerCore;

trait FlashMessenger
{
    protected $lotgdFlashMessenger;

    /**
     * Get FlashMessenger instance.
     *
     * @return object|null
     */
    public function getFlashMessenger()
    {
        if (! $this->lotgdFlashMessenger instanceof FlashMessengerCore)
        {
            $this->lotgdFlashMessenger = $this->getContainer(FlashMessengerCore::class);
        }

        return $this->lotgdFlashMessenger;
    }
}
