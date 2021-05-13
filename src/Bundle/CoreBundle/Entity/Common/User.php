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
use Lotgd\Bundle\UserBundle\Entity\User as EntityUser;

/**
 * Trait for add user field to entity.
 */
trait User
{
    /**
     * @ORM\ManyToOne(targetEntity=EntityUser::class)
     */
    protected $user;

    public function getAvatar(): ?EntityUser
    {
        return $this->user;
    }

    public function setUser(?EntityUser $user): self
    {
        $this->user = $user;

        return $this;
    }
}
