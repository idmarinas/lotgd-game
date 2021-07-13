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

trait Ban
{
    /**
     * @var bool
     *
     * @ORM\Column(name="banoverride", type="boolean", nullable=true, options={"default": 0})
     */
    private $banoverride = 0;

    /**
     * Set the value of Banoverride.
     *
     * @param bool $banoverride
     *
     * @return self
     */
    public function setBanoverride($banoverride)
    {
        $this->banoverride = $banoverride;

        return $this;
    }

    /**
     * Get the value of Banoverride.
     */
    public function getBanoverride(): bool
    {
        return $this->banoverride;
    }
}
