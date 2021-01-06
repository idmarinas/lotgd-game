<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.11.0
 */

namespace Lotgd\Core\Service;

use Lotgd\Core\EventManagerAware;

class HookManagerFactory
{
    public function __invoke()
    {
        $eventManager = new EventManagerAware();

        return $eventManager->getEventManager();
    }
}
