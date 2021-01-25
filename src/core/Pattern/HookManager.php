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

trigger_error(HookManager::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
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
            $this->lotgdHookManager = $this->getService(Hook::class);
        }

        return $this->lotgdHookManager;
    }
}
