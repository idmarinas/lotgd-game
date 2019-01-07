<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Referers.
 *
 * @ORM\Table(name="referers",
 *     indexes={
 *         @ORM\Index(name="uri", columns={"uri"}),
 *         @ORM\Index(name="site", columns={"site"})
 *     }
 * )
 * @ORM\Entity
 */
class Referers
{
    /**
     * @var int
     *
     * @ORM\Column(name="refererid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $refererid;

    /**
     * @var string
     *
     * @ORM\Column(name="uri", type="string", length=1000, nullable=false)
     */
    private $uri;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last", type="datetime", nullable=false)
     */
    private $last;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=50, nullable=false)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="dest", type="string", length=255, nullable=false)
     */
    private $dest;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=40, nullable=false)
     */
    private $ip;

    /**
     * Set the value of Refererid.
     *
     * @param int refererid
     *
     * @return self
     */
    public function setRefererid($refererid)
    {
        $this->refererid = $refererid;

        return $this;
    }

    /**
     * Get the value of Refererid.
     *
     * @return int
     */
    public function getRefererid(): int
    {
        return $this->refererid;
    }

    /**
     * Set the value of Uri.
     *
     * @param string uri
     *
     * @return self
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get the value of Uri.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Set the value of Count.
     *
     * @param int count
     *
     * @return self
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get the value of Count.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Set the value of Last.
     *
     * @param \DateTime last
     *
     * @return self
     */
    public function setLast(\DateTime $last)
    {
        $this->last = $last;

        return $this;
    }

    /**
     * Get the value of Last.
     *
     * @return \DateTime
     */
    public function getLast(): \DateTime
    {
        return $this->last;
    }

    /**
     * Set the value of Site.
     *
     * @param string site
     *
     * @return self
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get the value of Site.
     *
     * @return string
     */
    public function getSite(): string
    {
        return $this->site;
    }

    /**
     * Set the value of Dest.
     *
     * @param string dest
     *
     * @return self
     */
    public function setDest($dest)
    {
        $this->dest = $dest;

        return $this;
    }

    /**
     * Get the value of Dest.
     *
     * @return string
     */
    public function getDest(): string
    {
        return $this->dest;
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
}
