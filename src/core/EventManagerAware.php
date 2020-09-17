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

namespace Lotgd\Core;

use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManager;
use Lotgd\Core\EventManager\EventManager as CoreEventManager;

class EventManagerAware implements EventManagerAwareInterface
{
    protected $events;

    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers([
            __NAMESPACE__,
            \get_called_class(),
        ]);
        $this->events = $events;

        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->events)
        {
            $this->setEventManager(new CoreEventManager(new SharedEventManager()));
        }

        return $this->events;
    }
}
