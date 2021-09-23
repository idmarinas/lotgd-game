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

namespace Lotgd\Core\Entity\User;

use Lotgd\Core\Entity\Avatar as AvatarEntity;

trait Avatar
{
    /**
     * @ORM\OneToOne(targetEntity=AvatarEntity::class, cascade={"persist", "remove"})
     */
    private $avatar;

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
     * Alias.
     *
     * @deprecated 6.0.0 character is a reserve word and changed to avatar.
     */
    public function getCharacter()
    {
        return $this->getAvatar();
    }
}
