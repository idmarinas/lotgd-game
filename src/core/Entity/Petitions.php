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

/**
 * Petitions.
 *
 * @ORM\Table(name="petitions")
 * @ORM\Entity(repositoryClass="Lotgd\Core\EntityRepository\PetitionsRepository")
 */
class Petitions
{
    /**
     * @var int
     *
     * @ORM\Column(name="petitionid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $petitionid;

    /**
     * @var int
     *
     * @ORM\Column(name="author", type="integer", nullable=false, options={"unsigned": true})
     */
    private $author = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $date = '0000-00-00 00:00:00';

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"unsigned": true})
     */
    private $status = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", length=65535, nullable=false)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="pageinfo", type="text", length=65535, nullable=false)
     */
    private $pageinfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closedate", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $closedate = '0000-00-00 00:00:00';

    /**
     * @var int
     *
     * @ORM\Column(name="closeuserid", type="integer", nullable=false, options={"unsigned": true})
     */
    private $closeuserid = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=40, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=32, nullable=false)
     */
    private $id;

    /**
     * Set the value of Petitionid.
     *
     * @param int petitionid
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
     *
     * @return int
     */
    public function getPetitionid(): int
    {
        return $this->petitionid;
    }

    /**
     * Set the value of Author.
     *
     * @param int author
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
     *
     * @return int
     */
    public function getAuthor(): int
    {
        return $this->author;
    }

    /**
     * Set the value of Date.
     *
     * @param \DateTime date
     *
     * @return self
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of Date.
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * Set the value of Status.
     *
     * @param int status
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
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the value of Body.
     *
     * @param string body
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
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Set the value of Pageinfo.
     *
     * @param string pageinfo
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
     *
     * @return string
     */
    public function getPageinfo(): string
    {
        return $this->pageinfo;
    }

    /**
     * Set the value of Closedate.
     *
     * @param \DateTime closedate
     *
     * @return self
     */
    public function setClosedate(\DateTime $closedate)
    {
        $this->closedate = $closedate;

        return $this;
    }

    /**
     * Get the value of Closedate.
     *
     * @return \DateTime
     */
    public function getClosedate(): \DateTime
    {
        return $this->closedate;
    }

    /**
     * Set the value of Closeuserid.
     *
     * @param int closeuserid
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
     *
     * @return int
     */
    public function getCloseuserid(): int
    {
        return $this->closeuserid;
    }

    /**
     * Set the value of Ip.
     *
     * @param string ip
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
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Set the value of Id.
     *
     * @param string id
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
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
