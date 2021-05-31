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

use Doctrine\ORM\Mapping as ORM;

/**
 * Faillog.
 *
 * @ORM\Table(name="faillog",
 *     indexes={
 *         @ORM\Index(name="date", columns={"date"}),
 *         @ORM\Index(name="acctid", columns={"acctid"}),
 *         @ORM\Index(name="ip", columns={"ip"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\EntityRepository\FaillogRepository")
 */
class Faillog
{
    /**
     * @var int
     *
     * @ORM\Column(name="eventid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $eventid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="post", type="array", nullable=false)
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
     * @ORM\Column(name="acctid", type="integer", nullable=true, options={"unsigned": true})
     */
    private $acctid;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=32, nullable=false)
     */
    private $id;

    /**
     * Configure some default values.
     */
    public function __construct()
    {
        $this->date = new \DateTime('0000-00-00 00:00:00');
    }

    /**
     * Set the value of Eventid.
     *
     * @param int $eventid
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
     */
    public function getEventid(): int
    {
        return $this->eventid;
    }

    /**
     * Set the value of Date.
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
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * Set the value of Post.
     *
     * @param string $post
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
     */
    public function getPost(): string
    {
        return $this->post;
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
     * Set the value of Acctid.
     *
     * @param int $acctid
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
     */
    public function getAcctid(): int
    {
        return $this->acctid;
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
