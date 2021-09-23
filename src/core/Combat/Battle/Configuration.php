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

trait Configuration
{
    /** Zone of battle, use in death messages, but can use for other purposes */
    protected $battleZone = 'forest';

    /** Indicating if battle create a news when player lost */
    protected $createNews = true;

    /** Indicating if lost gold when lost in battle */
    protected $lostGold = true;

    /** Indicating if gain gold when win in battle */
    protected $gainGold;

    /** Indicating if gain gem when win in battle */
    protected $gainGem;

    /** Indicating if lost exp when lost in battle */
    protected $lostExp = true;

    /** Indicating if gains exp when win in battle */
    protected $gainExp = false;

    /** Indicating if gains favor when win in battle */
    protected $gainFavor = false;

    /** Indicating if die when lost in battle */
    protected $die = true;

    /** Indicating if flawless for perfect battle is allowed or not */
    protected $flawless = true;

    /** Indicating if process victory or defeat battle. False when you want simulate a battle. */
    protected $process = true;

    /**
     * Get the value of battleZone.
     */
    public function getBattleZone(): string
    {
        return $this->battleZone ?: $this->getOptionType();
    }

    /**
     * Set value where the battle takes place.
     * Game recognizes the forest and the graveyard, but can add more as you need
     */
    public function setBattleZone(string $battleZone): self
    {
        $this->battleZone = $battleZone;
        $this->setOptionType($battleZone);

        return $this;
    }

    /**
     * Get if is allow create news on battle lost.
     */
    public function allowCreateNews(): bool
    {
        return $this->createNews;
    }

    /**
     * Enable create news on battle lost.
     */
    public function enableCreateNews(): self
    {
        $this->createNews = true;

        return $this;
    }

    /**
     * Disable create news on battle lost.
     */
    public function disableCreateNews(): self
    {
        $this->createNews = false;

        return $this;
    }

    /**
     * Get if player can lost gold when lost in battle.
     */
    public function canLostGold(): bool
    {
        return $this->lostGold;
    }

    /**
     * Enable lost gold when lost in battle.
     */
    public function enableLostGold(): self
    {
        $this->lostGold = true;

        return $this;
    }

    /**
     * Disable lost gold when lost in battle.
     */
    public function disableLostGold(): self
    {
        $this->lostGold = false;

        return $this;
    }

    /**
     * Get if player can gain gold when win in battle.
     */
    public function canGainGold(): bool
    {
        //-- Always gain gold in forest if not explicitly disabled
        if ('forest' == $this->getBattleZone() && $this->gainGold === null)
        {
            return true;
        }
        //-- Not gain gold in graveyard (not allow included if explicitly enable)
        elseif ('graveyard' == $this->getBattleZone())
        {
            return false;
        }

        return $this->gainGold ?: false;
    }

    /**
     * Enable gain gold when win in battle.
     */
    public function enableGainGold(): self
    {
        $this->gainGold = true;

        return $this;
    }

    /**
     * Disable gain gold when win in battle.
     */
    public function disableGainGold(): self
    {
        $this->gainGold = false;

        return $this;
    }

    /**
     * Get if player can gain gem when win in battle.
     */
    public function canGainGem(): bool
    {
        //-- Always gain gem in forest if not explicitly disabled
        if ('forest' == $this->getBattleZone() && $this->gainGem === null)
        {
            return true;
        }
        //-- Not gain gem in graveyard (not allow included if explicitly enable)
        elseif ('graveyard' == $this->getBattleZone())
        {
            return false;
        }

        return $this->gainGem ?: false;
    }

    /**
     * Enable gain gem when win in battle.
     */
    public function enableGainGem(): self
    {
        $this->gainGem = true;

        return $this;
    }

    /**
     * Disable gain gem when win in battle.
     */
    public function disableGainGem(): self
    {
        $this->gainGem = false;

        return $this;
    }

    /**
     * Get if player can lost exp when lost in battle.
     */
    public function canLostExp(): bool
    {
        return $this->lostExp;
    }

    /**
     * Enable player lost exp when lost in battle.
     */
    public function enableLostExp(): self
    {
        $this->lostExp = true;

        return $this;
    }

    /**
     * Disable player lost exp when lost in battle.
     */
    public function disableLostExp(): self
    {
        $this->lostExp = false;

        return $this;
    }

    /**
     * Get if player can gain exp when win in battle
     */
    public function canGainExp(): bool
    {
        //-- Always gain forest in forest
        if ('forest' == $this->getBattleZone())
        {
            return true;
        }
        //-- Not gain exp if in graveyard
        elseif ('graveyard' == $this->getBattleZone())
        {
            return false;
        }

        return $this->gainExp;
    }

    /**
     * Enable player gain exp when win in battle.
     */
    public function enableGainExp(): self
    {
        $this->gainExp = true;

        return $this;
    }

    /**
     * Disable player gain exp when win in battle.
     */
    public function disableGainExp(): self
    {
        $this->gainExp = false;

        return $this;
    }

    /**
     * Get if player can gain favor when win in battle.
     */
    public function canGainFavor(): bool
    {
        //-- Always gain favor in graveyard
        if ('graveyard' == $this->getBattleZone())
        {
            return true;
        }
        //-- Not gain favor in forest
        elseif ('forest' == $this->getBattleZone())
        {
            return false;
        }

        return $this->gainFavor;
    }

    /**
     * Enable player gain favor when win in battle..
     */
    public function enableGainFavor(): self
    {
        $this->gainFavor = true;

        return $this;
    }

    /**
     * Disable player gain favor when win in battle..
     */
    public function disableGainFavor(): self
    {
        $this->gainFavor = false;

        return $this;
    }

    /**
     * Get if player can die when lost in battle.
     */
    public function canDie(): bool
    {
        return $this->die;
    }

    /**
     * Enable player die when lost in battle.
     */
    public function enableDie(): self
    {
        $this->die = true;

        return $this;
    }

    /**
     * Disable player die when lost in battle.
     */
    public function disableDie(): self
    {
        $this->die = false;

        return $this;
    }

    /**
     * Get if is allow flawlees for perfect battle.
     */
    public function allowFlawless(): bool
    {
        return $this->flawless;
    }

    /**
     * Allow flawlees for perfect battle.
     */
    public function enableFlawless(): self
    {
        $this->flawless = true;

        return $this;
    }

    /**
     * Deny flawlees for perfect battle.
     */
    public function disableFlawless(): self
    {
        $this->flawless = false;

        return $this;
    }

    /**
     * Get if need process battle on victory/defeat.
     */
    public function allowProccessBatteResults(): bool
    {
        return $this->process;
    }

    /**
     * Enable process victory or defeat battle.
     * Disabled when you want simulate a battle.
     *
     * @param mixed $process
     */
    public function enableProccessBatteResults(): self
    {
        $this->process = true;

        return $this;
    }

    /**
     * Disable process victory or defeat battle.
     * When you want simulate a battle.
     *
     * @param mixed $process
     */
    public function disableProccessBatteResults(): self
    {
        $this->process = false;

        return $this;
    }
}
