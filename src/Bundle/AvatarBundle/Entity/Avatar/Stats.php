<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\AvatarBundle\Entity\Avatar;

use Doctrine\ORM\Mapping as ORM;

trait Stats
{
    /**
     * Set the value of Defense.
     *
     * @param int $defense
     *
     * @return self
     */
    public function setDefense($defense)
    {
        $this->defense = (int) $defense;

        return $this;
    }

    /**
     * Get the value of Defense.
     */
    public function getDefense(): int
    {
        return $this->defense;
    }

    /**
     * Set the value of Attack.
     *
     * @param int $attack
     *
     * @return self
     */
    public function setAttack($attack)
    {
        $this->attack = (int) $attack;

        return $this;
    }

    /**
     * Get the value of Attack.
     */
    public function getAttack(): int
    {
        return $this->attack;
    }
}
