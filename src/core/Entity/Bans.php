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

/**
 * Bans.
 *
 * @ORM\Table(name="bans")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\BansRepository")
 */
class Bans
{
    /**
     *
     * @ORM\Column(name="banexpire", type="datetime")
     */
    private ?\DateTimeInterface $banexpire = null;

    /**
     *
     * @ORM\Column(name="uniqueid", type="string", length=32)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private ?string $uniqueid = '';

    /**
     *
     * @ORM\Column(name="ipfilter", type="string", length=40)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private ?string $ipfilter = '';

    /**
     *
     * @ORM\Column(name="banreason", type="text", length=65535)
     */
    private ?string $banreason = null;

    /**
     *
     * @ORM\Column(name="banner", type="string", length=50)
     */
    private ?string $banner = null;

    /**
     *
     * @ORM\Column(name="lasthit", type="datetime", options={"default"="0000-00-00 00:00:00"})
     */
    private ?\DateTimeInterface $lasthit = null;

    public function __construct()
    {
        $this->lasthit = new DateTime('now');
    }

    /**
     * Set the value of Banexpire.
     *
     * @param \DateTime|\DateTimeImmutable $banexpire
     *
     * @return self
     */
    public function setBanexpire(DateTimeInterface $banexpire)
    {
        $this->banexpire = $banexpire;

        return $this;
    }

    /**
     * Get the value of Banexpire.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getBanexpire(): DateTimeInterface
    {
        return $this->banexpire;
    }

    /**
     * Set the value of Uniqueid.
     *
     * @param string $uniqueid
     *
     * @return self
     */
    public function setUniqueid($uniqueid)
    {
        $this->uniqueid = $uniqueid;

        return $this;
    }

    /**
     * Get the value of Uniqueid.
     */
    public function getUniqueid(): string
    {
        return $this->uniqueid;
    }

    /**
     * Set the value of Ipfilter.
     *
     * @param string $ipfilter
     *
     * @return self
     */
    public function setIpfilter($ipfilter)
    {
        $this->ipfilter = $ipfilter;

        return $this;
    }

    /**
     * Get the value of Ipfilter.
     */
    public function getIpfilter(): string
    {
        return $this->ipfilter;
    }

    /**
     * Set the value of Banreason.
     *
     * @param string $banreason
     *
     * @return self
     */
    public function setBanreason($banreason)
    {
        $this->banreason = $banreason;

        return $this;
    }

    /**
     * Get the value of Banreason.
     */
    public function getBanreason(): string
    {
        return $this->banreason;
    }

    /**
     * Set the value of Banner.
     *
     * @param string $banner
     *
     * @return self
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get the value of Banner.
     */
    public function getBanner(): string
    {
        return $this->banner;
    }

    /**
     * Set the value of Lasthit.
     *
     * @param \DateTime|\DateTimeImmutable $lasthit
     *
     * @return self
     */
    public function setLasthit(DateTimeInterface $lasthit)
    {
        $this->lasthit = $lasthit;

        return $this;
    }

    /**
     * Get the value of Lasthit.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getLasthit(): DateTimeInterface
    {
        return $this->lasthit;
    }
}
