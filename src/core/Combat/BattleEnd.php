<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat;

trait BattleEnd
{
    /**
     * Indicate if battle is ended.
     *
     * @var bool
     */
    private $battleIsEnded = false;

    /**
     * Finalize battle.
     */
    public function battleEnd(): self
    {
        if ( ! $this->battleIsProcessed)
        {
            throw new \LogicException('The battle cannot be finalized if it is not processed first. Call Battle::battleProcess() before Battle::battleEnd().');
        }

        $this->battleHasWinner = false;

        //-- Enemies die and player alive: Player Win
        if ($this->countEnemiesAlive() <= 0 && $this->user['hitpoints'] > 0)
        {
            $this->victory = true;
            $this->defeat  = false;

            $this->battleHasWinner = true;
        }
        //-- Player die: Enemy Win
        elseif ($this->user['hitpoints'] <= 0)
        {
            $this->victory = false;
            $this->defeat  = true;

            $this->battleHasWinner = true;
        }

        //-- Proccess victory or defeat of battle
        $this->processBatteResults();

        $this->battleIsEnded = true;

        $this->updateData();

        return $this;
    }
}
