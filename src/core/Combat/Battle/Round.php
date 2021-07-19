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

trait Round
{
    /** Current round in battle (same as total rounds) */
    protected $round = 1;

    /**
     * Get the value of round.
     */
    public function getRound(): int
    {
        return $this->round;
    }

    /**
     * Set the value of round.
     */
    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Increase round by 1.
     */
    public function increaseRound(): self
    {
        ++$this->round;

        return $this;
    }
}
