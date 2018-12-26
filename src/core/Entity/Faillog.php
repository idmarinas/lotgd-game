<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faillog.
 *
 * @ORM\Table(name="faillog",
 *      indexes={
 *          @ORM\Index(name="date", columns={"date"}),
 *          @ORM\Index(name="acctid", columns={"acctid"}),
 *          @ORM\Index(name="ip", columns={"ip"})
 *      }
 * )
 * @ORM\Entity
 */
class Faillog
{
    /**
     * @var int
     *
     * @ORM\Column(name="eventid", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $eventid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
     */
    private $date = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="post", type="text", length=255, nullable=false)
     */
    private $post;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=40, nullable=false)
     */
    private $ip;

    /**
     * @var int
     *
     * @ORM\Column(name="acctid", type="integer", nullable=true, options={"unsigned":true})
     */
    private $acctid;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=32, nullable=false)
     */
    private $id;

    /**
     * Set the value of Eventid.
     *
     * @param int eventid
     *
     * @return self
     */
    public function setEventid($eventid)
    {
        $this->eventid = $eventid;

        return $this;
    }

    /**
     * Get the value of Eventid.
     *
     * @return int
     */
    public function getEventid(): int
    {
        return $this->eventid;
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
     * Set the value of Post.
     *
     * @param string post
     *
     * @return self
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get the value of Post.
     *
     * @return string
     */
    public function getPost(): string
    {
        return $this->post;
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
     * Set the value of Acctid.
     *
     * @param int acctid
     *
     * @return self
     */
    public function setAcctid($acctid)
    {
        $this->acctid = $acctid;

        return $this;
    }

    /**
     * Get the value of Acctid.
     *
     * @return int
     */
    public function getAcctid(): int
    {
        return $this->acctid;
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
