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

use Doctrine\Common\Collections\Collection;
use Lotgd\Core\Entity\Avatar as AvatarEntity;

trait Avatar
{
    /**
     * @ORM\OneToOne(targetEntity=Avatar::class, cascade={"persist", "remove"})
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity=Avatar::class, mappedBy="user")
     */
    private $avatars;

    public function getAvatar(): ?AvatarEntity
    {
        return $this->avatar;
    }

    public function setAvatar(?AvatarEntity $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Avatar[]
     */
    public function getAvatars(): Collection
    {
        return $this->avatars;
    }

    public function addAvatar(AvatarEntity $avatar): self
    {
        if ( ! $this->avatars->contains($avatar))
        {
            $this->avatars[] = $avatar;
            $avatar->setUser($this);
        }

        return $this;
    }

    public function removeAvatar(AvatarEntity $avatar): self
    {
        // set the owning side to null (unless already changed)
        if ($this->avatars->removeElement($avatar) && $avatar->getUser() === $this)
        {
            $avatar->setUser(null);
        }

        return $this;
    }
}
