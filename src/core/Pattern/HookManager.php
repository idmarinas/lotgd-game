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

use Lotgd\Core\EventManager\EventManager as CoreEventManager;
use Lotgd\Core\EventManager\Hook;

trait HookManager
{
    protected $lotgdHookManager;

    /**
     * Get EventManager instance.
     */
    public function getHookManager(): CoreEventManager
    {
        if ( ! $this->lotgdHookManager instanceof CoreEventManager)
        {
            $this->lotgdHookManager = $this->getContainer(Hook::class);
        }

        return $this->lotgdHookManager;
    }
}
