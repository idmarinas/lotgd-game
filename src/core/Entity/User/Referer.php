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

trait Referer
{
    /**
     * @var int
     *
     * @ORM\Column(name="referer", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $referer = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="refererawarded", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $refererawarded = 0;

    /**
     * Set the value of Referer.
     *
     * @return self
     */
    public function setReferer(int $referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get the value of Referer.
     */
    public function getReferer(): int
    {
        return $this->referer;
    }

    /**
     * Set the value of Refererawarded.
     *
     * @param int $refererawarded
     *
     * @return self
     */
    public function setRefererawarded($refererawarded)
    {
        $this->refererawarded = $refererawarded;

        return $this;
    }

    /**
     * Get the value of Refererawarded.
     */
    public function getRefererawarded(): int
    {
        return $this->refererawarded;
    }
}
