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

use DateTimeInterface;
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
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\ReferersRepository")
 */
class Referers
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="refererid", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $refererid;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=1000)
     */
    private $uri;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $count = 0;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     */
    private $last;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=50)
     */
    private $site;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     */
    private $dest;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=40)
     */
    private $ip;

    /**
     * Set the value of Refererid.
     *
     * @param int $refererid
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
     */
    public function getRefererid(): int
    {
        return $this->refererid;
    }

    /**
     * Set the value of Uri.
     *
     * @param string $uri
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
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Set the value of Count.
     *
     * @param int $count
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
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Increment count by 1.
     *
     * @return self
     */
    public function incrementCount()
    {
        ++$this->count;

        return $this;
    }

    /**
     * Set the value of Last.
     *
     * @param \DateTime|\DateTimeImmutable $last
     *
     * @return self
     */
    public function setLast(DateTimeInterface $last)
    {
        $this->last = $last;

        return $this;
    }

    /**
     * Get the value of Last.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getLast(): DateTimeInterface
    {
        return $this->last;
    }

    /**
     * Set the value of Site.
     *
     * @param string $site
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
     */
    public function getSite(): string
    {
        return $this->site;
    }

    /**
     * Set the value of Dest.
     *
     * @param string $dest
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
     */
    public function getDest(): string
    {
        return $this->dest;
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
}
