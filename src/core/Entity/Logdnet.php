<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logdnet.
 *
 * @ORM\Table(name="logdnet")
 * @ORM\Entity
 */
class Logdnet
{
    /**
     * @var int
     *
     * @ORM\Column(name="serverid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $serverid;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="priority", type="float", precision=10, scale=0, nullable=false)
     */
    private $priority = '100';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdate", type="datetime", nullable=false)
     */
    private $lastupdate = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=255, nullable=false)
     */
    private $version = 'Unknown';

    /**
     * @var string
     *
     * @ORM\Column(name="admin", type="string", length=255, nullable=false)
     */
    private $admin = 'unknown';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastping", type="datetime", nullable=false)
     */
    private $lastping = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="recentips", type="string", length=255, nullable=false)
     */
    private $recentips;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=20, nullable=false)
     */
    private $lang;

    /**
     * Set the value of Serverid.
     *
     * @param int serverid
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
     *
     * @return int
     */
    public function getServerid(): int
    {
        return $this->serverid;
    }

    /**
     * Set the value of Address.
     *
     * @param string address
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
     *
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Set the value of Description.
     *
     * @param string description
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
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of Priority.
     *
     * @param float priority
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
     *
     * @return float
     */
    public function getPriority(): float
    {
        return $this->priority;
    }

    /**
     * Set the value of Lastupdate.
     *
     * @param \DateTime lastupdate
     *
     * @return self
     */
    public function setLastupdate(\DateTime $lastupdate)
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    /**
     * Get the value of Lastupdate.
     *
     * @return \DateTime
     */
    public function getLastupdate(): \DateTime
    {
        return $this->lastupdate;
    }

    /**
     * Set the value of Version.
     *
     * @param string version
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
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Set the value of Admin.
     *
     * @param string admin
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
     *
     * @return string
     */
    public function getAdmin(): string
    {
        return $this->admin;
    }

    /**
     * Set the value of Lastping.
     *
     * @param \DateTime lastping
     *
     * @return self
     */
    public function setLastping(\DateTime $lastping)
    {
        $this->lastping = $lastping;

        return $this;
    }

    /**
     * Get the value of Lastping.
     *
     * @return \DateTime
     */
    public function getLastping(): \DateTime
    {
        return $this->lastping;
    }

    /**
     * Set the value of Recentips.
     *
     * @param string recentips
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
     *
     * @return string
     */
    public function getRecentips(): string
    {
        return $this->recentips;
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
     * Set the value of Lang.
     *
     * @param string lang
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
     *
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }
}
