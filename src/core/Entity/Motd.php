<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Motd.
 *
 * @ORM\Table(name="motd")
 * @ORM\Entity
 */
class Motd
{
    /**
     * @var int
     *
     * @ORM\Column(name="motditem", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $motditem;

    /**
     * @var string
     *
     * @ORM\Column(name="motdtitle", type="string", length=200, nullable=true)
     */
    private $motdtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="motdbody", type="text", length=65535, nullable=false)
     */
    private $motdbody;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="motddate", type="datetime", nullable=true)
     */
    private $motddate;

    /**
     * @var bool
     *
     * @ORM\Column(name="motdtype", type="boolean", nullable=false, options={"default":"0"})
     */
    private $motdtype = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="motdauthor", type="integer", nullable=false, options={"unsigned":true, "default":"0"})
     */
    private $motdauthor = 0;

    /**
     * Set the value of Motditem.
     *
     * @param int motditem
     *
     * @return self
     */
    public function setMotditem($motditem)
    {
        $this->motditem = $motditem;

        return $this;
    }

    /**
     * Get the value of Motditem.
     *
     * @return int
     */
    public function getMotditem(): int
    {
        return $this->motditem;
    }

    /**
     * Set the value of Motdtitle.
     *
     * @param string motdtitle
     *
     * @return self
     */
    public function setMotdtitle($motdtitle)
    {
        $this->motdtitle = $motdtitle;

        return $this;
    }

    /**
     * Get the value of Motdtitle.
     *
     * @return string
     */
    public function getMotdtitle(): string
    {
        return $this->motdtitle;
    }

    /**
     * Set the value of Motdbody.
     *
     * @param string motdbody
     *
     * @return self
     */
    public function setMotdbody($motdbody)
    {
        $this->motdbody = $motdbody;

        return $this;
    }

    /**
     * Get the value of Motdbody.
     *
     * @return string
     */
    public function getMotdbody(): string
    {
        return $this->motdbody;
    }

    /**
     * Set the value of Motddate.
     *
     * @param \DateTime motddate
     *
     * @return self
     */
    public function setMotddate(\DateTime $motddate)
    {
        $this->motddate = $motddate;

        return $this;
    }

    /**
     * Get the value of Motddate.
     *
     * @return \DateTime
     */
    public function getMotddate(): \DateTime
    {
        return $this->motddate;
    }

    /**
     * Set the value of Motdtype.
     *
     * @param bool motdtype
     *
     * @return self
     */
    public function setMotdtype($motdtype)
    {
        $this->motdtype = $motdtype;

        return $this;
    }

    /**
     * Get the value of Motdtype.
     *
     * @return bool
     */
    public function getMotdtype(): bool
    {
        return $this->motdtype;
    }

    /**
     * Set the value of Motdauthor.
     *
     * @param int motdauthor
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
     *
     * @return int
     */
    public function getMotdauthor(): int
    {
        return $this->motdauthor;
    }
}
