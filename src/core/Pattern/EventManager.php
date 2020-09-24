<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\EventManager\Event;
use Lotgd\Core\EventManager\EventManager as CoreEventManager;

trait EventManager
{
    protected $lotgdEventManager;

    /**
     * Get EventManager instance.
     */
    public function getEventManager(): CoreEventManager
    {
        if ( ! $this->lotgdEventManager instanceof CoreEventManager)
        {
            $this->lotgdEventManager = $this->getContainer(Event::class);
        }

        return $this->lotgdEventManager;
    }
}
