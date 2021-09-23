<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Entity;

use Bukashk0zzz\FilterBundle\Annotation\FilterAnnotation as Filter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Motd.
 *
 * @ORM\Table(name="motd")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\MotdRepository")
 */
class Motd
{
    /**
     * @var int
     *
     * @ORM\Column(name="motditem", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $motditem;

    /**
     * @var string
     *
     * @ORM\Column(name="motdtitle", type="string", length=200, nullable=true)
     *
     * @Filter("StripTags")
     *
     * @Assert\NotNull
     * @Assert\Length(
     *     min=1,
     *     max=200
     * )
     */
    private $motdtitle = '';

    /**
     * @var string
     *
     * @ORM\Column(name="motdbody", type="text", length=65535, nullable=false)
     *
     * @Filter("StripTags")
     * @Filter("StringTrim")
     *
     * @Assert\NotNull
     * @Assert\Length(
     *     min=1,
     *     max=65535
     * )
     */
    private $motdbody = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="motddate", type="datetime", nullable=true, options={"default": "0000-00-00 00:00:00"})
     */
    private $motddate;

    /**
     * @var bool
     *
     * @ORM\Column(name="motdtype", type="boolean", nullable=false, options={"default": "0"})
     */
    private $motdtype = false;

    /**
     * @var int
     *
     * @ORM\Column(name="motdauthor", type="integer", nullable=false, options={"unsigned": true, "default": "0"})
     *
     * @Assert\DivisibleBy(1)
     */
    private $motdauthor = 0;

    public function __construct()
    {
        $this->motddate = new \DateTime('now');
    }

    /**
     * Set the value of Motditem.
     *
     * @param int $motditem
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
     */
    public function getMotditem(): ?int
    {
        return $this->motditem;
    }

    /**
     * Set the value of Motdtitle.
     *
     * @param string $motdtitle
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
     */
    public function getMotdtitle(): string
    {
        return $this->motdtitle;
    }

    /**
     * Set the value of Motdbody.
     *
     * @param string $motdbody
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
     */
    public function getMotdbody(): string
    {
        return $this->motdbody;
    }

    /**
     * Set the value of Motddate.
     *
     * @param \DateTime|\DateTimeImmutable $motddate
     *
     * @return self
     */
    public function setMotddate(\DateTimeInterface $motddate)
    {
        $this->motddate = $motddate;

        return $this;
    }

    /**
     * Get the value of Motddate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getMotddate(): \DateTimeInterface
    {
        return $this->motddate;
    }

    /**
     * Set the value of Motdtype.
     *
     * @param bool $motdtype
     *
     * @return self
     */
    public function setMotdtype($motdtype)
    {
        $this->motdtype = (bool) $motdtype;

        return $this;
    }

    /**
     * Get the value of Motdtype.
     */
    public function getMotdtype(): bool
    {
        return $this->motdtype;
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
}
