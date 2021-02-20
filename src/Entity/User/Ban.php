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

namespace Lotgd\Core\Entity\User;

trait Ban
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $bannedUntil;

    public function getBannedUntil(): ?\DateTimeInterface
    {
        return $this->bannedUntil;
    }

    public function setBannedUntil(?\DateTimeInterface $bannedUntil): self
    {
        $this->bannedUntil = $bannedUntil;

        return $this;
    }

    //-- Check if user is banned
    public function isBanned(): bool
    {
        if (
            ! $this->bannedUntil instanceof \DateTimeInterface
            || '-0001-11-30' == $this->bannedUntil->format('Y-m-d')
        ) {
            return false;
        }

        return $this->bannedUntil > (new \DateTime('now'));
    }
}
