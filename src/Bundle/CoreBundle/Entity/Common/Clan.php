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
use Lotgd\Bundle\CoreBundle\Entity as CoreEntity;

/**
 * Trait for add clan field to entity.
 */
trait Clan
{
    /**
     * @ORM\ManyToOne(targetEntity=CoreEntity\Clans::class)
     * @ORM\JoinColumn(referencedColumnName="clanid")
     */
    protected $clan;

    public function getClan(): ?CoreEntity\Clans
    {
        return $this->clan;
    }

    public function setClan(?CoreEntity\Clans $clan): self
    {
        $this->clan = $clan;

        return $this;
    }

}
