<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.0
 */

namespace Lotgd\Core\Event;

use Symfony\Contracts\EventDispatcher\Event;

class Graveyard extends Event
{
    /**
     * Hooks of graveyard.
     */
    // Start a battle
    public const FIGHT_START = 'lotgd.graveyard.fight.start';

    // Question, actions
    public const DEATH_OVERLORD_ACTIONS = 'lotgd.graveyard.death.overlord.actions';

    // Question, actions
    public const DEATH_OVERLORD_FAVORS = 'lotgd.graveyard.death.overlord.favors';

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
