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

trait Hitpoints
{
    /**
     * @ORM\Column(name="hitpoints", type="integer", nullable=false, options={"default": 10, "unsigned": true})
     */
    protected $hitpoints = 10;

    /**
     * @ORM\Column(name="maxhitpoints", type="integer", nullable=false, options={"default": 10, "unsigned": true})
     */
    protected $maxhitpoints = 10;

    /**
     * @ORM\Column(name="permahitpoints", type="integer", nullable=false, options={"default": 0})
     */
    protected $permahitpoints = 0;

    /**
     * Set the value of Hitpoints.
     */
    public function setHitpoints(int $hitpoints): self
    {
        $this->hitpoints = $hitpoints;

        return $this;
    }

    /**
     * Get the value of Hitpoints.
     */
    public function getHitpoints(): int
    {
        return $this->hitpoints;
    }

    /**
     * Set the value of Maxhitpoints.
     *
     * @return self
     */
    public function setMaxhitpoints(int $maxhitpoints)
    {
        $this->maxhitpoints = $maxhitpoints;

        return $this;
    }

    /**
     * Get the value of Maxhitpoints.
     */
    public function getMaxhitpoints(): int
    {
        return $this->maxhitpoints;
    }

    /**
     * Set the value of Permahitpoints.
     */
    public function setPermahitpoints(int $permahitpoints): self
    {
        $this->permahitpoints = $permahitpoints;

        return $this;
    }

    /**
     * Get the value of Permahitpoints.
     */
    public function getPermahitpoints(): int
    {
        return $this->permahitpoints;
    }

    public function isAlive(): bool
    {
        return $this->getHitpoints() > 0;
    }
}
