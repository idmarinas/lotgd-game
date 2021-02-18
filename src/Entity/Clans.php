<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Clans.
 *
 * @ORM\Table(name="clans",
 *     indexes={
 *         @ORM\Index(name="clanname", columns={"clanname"}),
 *         @ORM\Index(name="clanshort", columns={"clanshort"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\EntityRepository\ClansRepository")
 *
 * @UniqueEntity("clanname")
 */
class Clans
{
    /**
     * @var int
     *
     * @ORM\Column(name="clanid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $clanid;

    /**
     * @var string
     *
     * @ORM\Column(name="clanname", type="string", length=255, nullable=false)
     *
     * @Assert\Length(
     *     min=2,
     *     max=250
     * )
     * @Assert\Regex(
     *     pattern="/[^\p{L} '-]/",
     *     match=false,
     * )
     */
    private $clanname;

    /**
     * @var string
     *
     * @ORM\Column(name="clanshort", type="string", length=50, nullable=false)
     *
     * @Assert\Length(
     *     min=2,
     *     max=5
     * )
     * @Assert\Regex(
     *     pattern="/[^[:alpha:]]/",
     *     match=false,
     * )
     */
    private $clanshort;

    /**
     * @var string
     *
     * @ORM\Column(name="clanmotd", type="text", nullable=false, length=65535)
     */
    private $clanmotd = '';

    /**
     * @var string
     *
     * @ORM\Column(name="clandesc", type="text", nullable=false, length=65535)
     */
    private $clandesc = '';

    /**
     * @var int
     *
     * @ORM\Column(name="motdauthor", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $motdauthor = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="descauthor", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $descauthor = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="customsay", type="string", length=15, nullable=false)
     */
    private $customsay = '';

    /**
     * Set the value of Clanid.
     *
     * @param int $clanid
     *
     * @return self
     */
    public function setClanid($clanid)
    {
        $this->clanid = $clanid;

        return $this;
    }

    /**
     * Get the value of Clanid.
     */
    public function getClanid(): int
    {
        return $this->clanid;
    }

    /**
     * Set the value of Clanname.
     *
     * @param string $clanname
     *
     * @return self
     */
    public function setClanname($clanname)
    {
        $this->clanname = $clanname;

        return $this;
    }

    /**
     * Get the value of Clanname.
     */
    public function getClanname(): string
    {
        return $this->clanname;
    }

    /**
     * Set the value of Clanshort.
     *
     * @param string $clanshort
     *
     * @return self
     */
    public function setClanshort($clanshort)
    {
        $this->clanshort = $clanshort;

        return $this;
    }

    /**
     * Get the value of Clanshort.
     */
    public function getClanshort(): string
    {
        return $this->clanshort;
    }

    /**
     * Set the value of Clanmotd.
     *
     * @param string $clanmotd
     *
     * @return self
     */
    public function setClanmotd($clanmotd)
    {
        $this->clanmotd = $clanmotd;

        return $this;
    }

    /**
     * Get the value of Clanmotd.
     */
    public function getClanmotd(): string
    {
        return $this->clanmotd;
    }

    /**
     * Set the value of Clandesc.
     *
     * @param string $clandesc
     *
     * @return self
     */
    public function setClandesc($clandesc)
    {
        $this->clandesc = $clandesc;

        return $this;
    }

    /**
     * Get the value of Clandesc.
     */
    public function getClandesc(): string
    {
        return $this->clandesc;
    }

    /**
     * Set the value of Motdauthor.
     *
     * @param int $motdauthor
     *
     * @return self
     */
    public function setMotdauthor($motdauthor)
    {
        $this->motdauthor = $motdauthor;

        return $this;
    }

    /**
     * Get the value of Motdauthor.
     */
    public function getMotdauthor(): int
    {
        return $this->motdauthor;
    }

    /**
     * Set the value of Descauthor.
     *
     * @param int $descauthor
     *
     * @return self
     */
    public function setDescauthor($descauthor)
    {
        $this->descauthor = $descauthor;

        return $this;
    }

    /**
     * Get the value of Descauthor.
     */
    public function getDescauthor(): int
    {
        return $this->descauthor;
    }

    /**
     * Set the value of Customsay.
     *
     * @param string $customsay
     *
     * @return self
     */
    public function setCustomsay($customsay)
    {
        $this->customsay = $customsay;

        return $this;
    }

    /**
     * Get the value of Customsay.
     */
    public function getCustomsay(): string
    {
        return $this->customsay;
    }
}
