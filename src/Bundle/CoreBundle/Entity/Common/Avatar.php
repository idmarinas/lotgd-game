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

namespace Lotgd\Bundle\CoreBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Lotgd\Bundle\AvatarBundle\Entity\Avatar as AvatarEntity;

/**
 * Trait for add avatar field to entity.
 */
trait Avatar
{
    /**
     * @ORM\ManyToOne(targetEntity=AvatarEntity::class)
     */
    protected $avatar;

    public function getAvatar(): ?AvatarEntity
    {
        return $this->avatar;
    }

    public function setAvatar(?AvatarEntity $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}
