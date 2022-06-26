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

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Petitions.
 *
 * @ORM\Table(name="petitions")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\PetitionsRepository")
 */
class Petitions
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="petitionid", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $petitionid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="author", type="integer", options={"unsigned"=true})
     */
    private $author = 0;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="date", type="datetime", options={"default"="0000-00-00 00:00:00"})
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="smallint", options={"unsigned"=true})
     */
    private $status = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="array")
     */
    private $body = '';

    /**
     * @var string
     *
     * @ORM\Column(name="pageinfo", type="array")
     */
    private $pageinfo = [];

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="closedate", type="datetime", options={"default"="0000-00-00 00:00:00"})
     */
    private $closedate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="closeuserid", type="integer", options={"unsigned"=true})
     *
     * @Assert\DivisibleBy(1)
     */
    private $closeuserid = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", length=40)
     */
    private $ip = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="id", type="string", length=32)
     */
    private $id = '';

    public function __construct()
    {
        $this->date      = new DateTime('0000-00-00 00:00:00');
        $this->closedate = new DateTime('0000-00-00 00:00:00');
    }

    /**
     * Set the value of Petitionid.
     *
     * @param int $petitionid
     *
     * @return self
     */
    public function setPetitionid($petitionid)
    {
        $this->petitionid = $petitionid;

        return $this;
    }

    /**
     * Get the value of Petitionid.
     */
    public function getPetitionid(): ?int
    {
        return $this->petitionid;
    }

    /**
     * Set the value of Author.
     *
     * @param int $author
     *
     * @return self
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of Author.
     */
    public function getAuthor(): int
    {
        return $this->author;
    }

    /**
     * Set the value of Date.
     *
     * @param \DateTime|\DateTimeImmutable $date
     *
     * @return self
     */
    public function setDate(DateTimeInterface $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of Date.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Set the value of Status.
     *
     * @param int $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of Status.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the value of Body.
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of Body.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Set the value of Pageinfo.
     *
     * @param string $pageinfo
     *
     * @return self
     */
    public function setPageinfo($pageinfo)
    {
        $this->pageinfo = $pageinfo;

        return $this;
    }

    /**
     * Get the value of Pageinfo.
     */
    public function getPageinfo(): string
    {
        return $this->pageinfo;
    }

    /**
     * Set the value of Closedate.
     *
     * @param \DateTime|\DateTimeImmutable $closedate
     *
     * @return self
     */
    public function setClosedate(DateTimeInterface $closedate)
    {
        $this->closedate = $closedate;

        return $this;
    }

    /**
     * Get the value of Closedate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getClosedate(): DateTimeInterface
    {
        return $this->closedate;
    }

    /**
     * Set the value of Closeuserid.
     *
     * @param int $closeuserid
     *
     * @return self
     */
    public function setCloseuserid($closeuserid)
    {
        $this->closeuserid = $closeuserid;

        return $this;
    }

    /**
     * Get the value of Closeuserid.
     */
    public function getCloseuserid(): int
    {
        return $this->closeuserid;
    }

    /**
     * Set the value of Ip.
     *
     * @param string $ip
     *
     * @return self
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get the value of Ip.
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Set the value of Id.
     *
     * @param string $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Id.
     */
    public function getId(): string
    {
        return $this->id;
    }
}
