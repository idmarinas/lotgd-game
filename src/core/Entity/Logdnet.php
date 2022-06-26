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
 * Logdnet.
 *
 * @ORM\Table(name="logdnet")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\LogdnetRepository")
 */
class Logdnet
{
    /**
     *
     * @ORM\Column(name="serverid", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $serverid = null;

    /**
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private ?string $address = null;

    /**
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private ?string $description = null;

    /**
     * @var float|null
     *
     * @ORM\Column(name="priority", type="float", precision=10, options={"default"="100"})
     */
    private float $priority = 100;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="lastupdate", type="datetime", options={"default"="0000-00-00 00:00:00"})
     */
    private ?\DateTimeInterface $lastupdate = null;

    /**
     *
     * @ORM\Column(name="version", type="string", length=255, options={"default"="Unknown"})
     */
    private ?string $version = 'Unknown';

    /**
     *
     * @ORM\Column(name="admin", type="string", length=255, options={"default"="unknown"})
     */
    private ?string $admin = 'unknown';

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="lastping", type="datetime")
     */
    private ?\DateTimeInterface $lastping = null;

    /**
     *
     * @ORM\Column(name="recentips", type="string", length=255)
     */
    private ?string $recentips = null;

    /**
     *
     * @ORM\Column(name="count", type="integer", options={"unsigned"=true})
     */
    private ?int $count = 0;

    /**
     *
     * @ORM\Column(name="lang", type="string", length=20)
     */
    private ?string $lang = null;

    public function __construct()
    {
        $this->lastupdate = new DateTime('now');
        $this->lastping = new DateTime('0000-00-00 00:00:00');
    }

    /**
     * Set the value of Serverid.
     *
     * @param int $serverid
     *
     * @return self
     */
    public function setServerid($serverid)
    {
        $this->serverid = $serverid;

        return $this;
    }

    /**
     * Get the value of Serverid.
     */
    public function getServerid(): int
    {
        return $this->serverid;
    }

    /**
     * Set the value of Address.
     *
     * @param string $address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of Address.
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Set the value of Description.
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of Description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of Priority.
     *
     * @param float $priority
     *
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get the value of Priority.
     */
    public function getPriority(): float
    {
        return $this->priority;
    }

    /**
     * Set the value of Lastupdate.
     *
     * @param \DateTime|\DateTimeImmutable $lastupdate
     *
     * @return self
     */
    public function setLastupdate(DateTimeInterface $lastupdate)
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    /**
     * Get the value of Lastupdate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getLastupdate(): DateTimeInterface
    {
        return $this->lastupdate;
    }

    /**
     * Set the value of Version.
     *
     * @param string $version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the value of Version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Set the value of Admin.
     *
     * @param string $admin
     *
     * @return self
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get the value of Admin.
     */
    public function getAdmin(): string
    {
        return $this->admin;
    }

    /**
     * Set the value of Lastping.
     *
     * @param \DateTime|\DateTimeImmutable $lastping
     *
     * @return self
     */
    public function setLastping(DateTimeInterface $lastping)
    {
        $this->lastping = $lastping;

        return $this;
    }

    /**
     * Get the value of Lastping.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getLastping(): DateTimeInterface
    {
        return $this->lastping;
    }

    /**
     * Set the value of Recentips.
     *
     * @param string $recentips
     *
     * @return self
     */
    public function setRecentips($recentips)
    {
        $this->recentips = $recentips;

        return $this;
    }

    /**
     * Get the value of Recentips.
     */
    public function getRecentips(): string
    {
        return $this->recentips;
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
     * Set the value of Lang.
     *
     * @param string $lang
     *
     * @return self
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get the value of Lang.
     */
    public function getLang(): string
    {
        return $this->lang;
    }
}
