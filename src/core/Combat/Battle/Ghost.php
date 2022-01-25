<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

use LogicException;

trait Ghost
{
    private $ghostActivated = false;
    private $ghostStats     = [];

    /**
     * Enable player as Ghost because is dead :(.
     *
     * This make changes necesary for Battle run correct when character is dead.
     */
    public function enableGhost(): self
    {
        if ($this->battleIsStarted || ! $this->battleIsInitalized)
        {
            throw new LogicException('The character cannot be activated as a ghost because the battle has already started or the battlefield has not been started.. Call Battle::enableGhost() before Battle::battleStart() but after Battle::initialize().');
        }

        $this->ghostStats['original_attack']    = $this->user['attack'];
        $this->ghostStats['original_defense']   = $this->user['defense'];
        $this->ghostStats['original_hitpoints'] = $this->user['hitpoints'];

        $this->user['hitpoints'] = $this->user['soulpoints'];

        $this->ghostActivated = true;

        return $this;
    }

    /**
     * Restore player, battle end.
     */
    public function disableGhost(): self
    {
        if ( ! $this->battleShowedResults || ! $this->ghostActivated)
        {
            throw new LogicException('The character cannot be restored because the results have not been processed and/or the ghost is not activated. Call Battle::enableGhost() and Battle::battleResults() before Battle::disableGhost().');
        }

        $this->user['soulpoints'] = $this->user['hitpoints'];

        $this->user['attack']    = $this->ghostStats['original_attack'];
        $this->user['defense']   = $this->ghostStats['original_defense'];
        $this->user['hitpoints'] = $this->ghostStats['original_hitpoints'];

        $this->ghostActivated = false;

        $this->updateData();

        return $this;
    }
}
