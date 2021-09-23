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

namespace Lotgd\Core\Entity\Avatar;

trait Clan
{
    /**
     * @var int
     *
     * @ORM\Column(name="clanid", type="integer", nullable=true, options={"default": 0, "unsigned": true})
     */
    private $clanid;

    /**
     * @var int
     *
     * @ORM\Column(name="clanrank", type="smallint", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $clanrank = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clanjoindate", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $clanjoindate;

    /**
     * Set the value of Clanid.
     *
     * @param int $clanid
     *
     * @return self
     */
    public function setClanid(?int $clanid)
    {
        $this->clanid = $clanid;

        return $this;
    }

    /**
     * Get the value of Clanid.
     */
    public function getClanid(): ?int
    {
        return $this->clanid;
    }

    /**
     * Set the value of Clanrank.
     *
     * @param int $clanrank
     *
     * @return self
     */
    public function setClanrank($clanrank)
    {
        $this->clanrank = (int) $clanrank;

        return $this;
    }

    /**
     * Get the value of Clanrank.
     */
    public function getClanrank(): int
    {
        return $this->clanrank;
    }

    /**
     * Set the value of Clanjoindate.
     *
     * @param \DateTime|\DateTimeImmutable $clanjoindate
     *
     * @return self
     */
    public function setClanjoindate(\DateTimeInterface $clanjoindate)
    {
        $this->clanjoindate = $clanjoindate;

        return $this;
    }

    /**
     * Get the value of Clanjoindate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getClanjoindate(): \DateTimeInterface
    {
        return $this->clanjoindate;
    }
}
