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

trait Dragon
{
    /**
     * @ORM\Column(type="integer", options={"default": 0, "unsigned": true})
     */
    protected $dragonkills = 0;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $seenDragon = 0;

    /**
     * @ORM\Column(type="array", options={"default": "a:0:{}"})
     */
    protected $dragonPoints = [];

    public function setDragonPoints(array $dragonPoints): self
    {
        $this->dragonPoints = $dragonPoints;

        return $this;
    }

    public function getDragonPoints(): array
    {
        return $this->dragonPoints;
    }

    public function setSeenDragon(bool $seenDragon): self
    {
        $this->seenDragon = $seenDragon;

        return $this;
    }

    public function getSeenDragon(): bool
    {
        return $this->seenDragon;
    }

    public function setDragonkills(int $dragonkills): self
    {
        $this->dragonkills = $dragonkills;

        return $this;
    }

    public function getDragonkills(): int
    {
        return $this->dragonkills;
    }
}
