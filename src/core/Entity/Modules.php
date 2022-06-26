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
 * Structure of table "modules" in data base.
 *
 * This table stores modules instaled in game.
 *
 * @ORM\Table(name="modules")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\ModulesRepository")
 */
class Modules
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="modulename", type="string", length=50)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $modulename;

    /**
     * @var string|null
     *
     * @ORM\Column(name="formalname", type="string", length=255)
     */
    private $formalname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="moduleauthor", type="string", length=255)
     */
    private $moduleauthor;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="active", type="boolean", options={"default"="0"})
     */
    private $active = false;

    /**
     * @var string|null
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="installdate", type="datetime", options={"default"="0000-00-00 00:00:00"})
     */
    private $installdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="installedby", type="string", length=50)
     */
    private $installedby;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="filemoddate", type="datetime", options={"default"="0000-00-00 00:00:00"})
     */
    private $filemoddate;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="type", type="boolean", options={"default"="0"})
     */
    private $type = false;

    /**
     * @var string|null
     *
     * @ORM\Column(name="extras", type="text", length=65535, nullable=true)
     */
    private $extras;

    /**
     * @var string|null
     *
     * @ORM\Column(name="category", type="string", length=50)
     */
    private $category;

    /**
     * @var string|null
     *
     * @ORM\Column(name="infokeys", type="text", length=65535)
     */
    private $infokeys;

    /**
     * @var string|null
     *
     * @ORM\Column(name="version", type="string", length=10)
     */
    private $version;

    /**
     * @var string|null
     *
     * @ORM\Column(name="download", type="string", length=200)
     */
    private $download;

    /**
     * Configure some default values.
     */
    public function __construct()
    {
        $this->installdate = new DateTime('0000-00-00 00:00:00');
        $this->filemoddate = new DateTime('0000-00-00 00:00:00');
    }

    /**
     * Set the value of Modulename.
     *
     * @param string $modulename
     *
     * @return self
     */
    public function setModulename($modulename)
    {
        $this->modulename = $modulename;

        return $this;
    }

    /**
     * Get the value of Modulename.
     */
    public function getModulename(): string
    {
        return $this->modulename;
    }

    /**
     * Set the value of Formalname.
     *
     * @param string $formalname
     *
     * @return self
     */
    public function setFormalname($formalname)
    {
        $this->formalname = $formalname;

        return $this;
    }

    /**
     * Get the value of Formalname.
     */
    public function getFormalname(): string
    {
        return $this->formalname;
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
     * Set the value of Moduleauthor.
     *
     * @param string $moduleauthor
     *
     * @return self
     */
    public function setModuleauthor($moduleauthor)
    {
        $this->moduleauthor = $moduleauthor;

        return $this;
    }

    /**
     * Get the value of Moduleauthor.
     */
    public function getModuleauthor(): string
    {
        return $this->moduleauthor;
    }

    /**
     * Set the value of Active.
     *
     * @param bool $active
     *
     * @return self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of Active.
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * Set the value of Filename.
     *
     * @param string $filename
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the value of Filename.
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Set the value of Installdate.
     *
     * @param \DateTime|\DateTimeImmutable $installdate
     *
     * @return self
     */
    public function setInstalldate(DateTimeInterface $installdate)
    {
        $this->installdate = $installdate;

        return $this;
    }

    /**
     * Get the value of Installdate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getInstalldate(): DateTimeInterface
    {
        return $this->installdate;
    }

    /**
     * Set the value of Installedby.
     *
     * @param string $installedby
     *
     * @return self
     */
    public function setInstalledby($installedby)
    {
        $this->installedby = $installedby;

        return $this;
    }

    /**
     * Get the value of Installedby.
     */
    public function getInstalledby(): string
    {
        return $this->installedby;
    }

    /**
     * Set the value of Filemoddate.
     *
     * @param \DateTime|\DateTimeImmutable $filemoddate
     *
     * @return self
     */
    public function setFilemoddate(DateTimeInterface $filemoddate)
    {
        $this->filemoddate = $filemoddate;

        return $this;
    }

    /**
     * Get the value of Filemoddate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getFilemoddate(): DateTimeInterface
    {
        return $this->filemoddate;
    }

    /**
     * Set the value of Type.
     *
     * @param bool $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of Type.
     */
    public function getType(): bool
    {
        return $this->type;
    }

    /**
     * Set the value of Extras.
     *
     * @param string $extras
     *
     * @return self
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Get the value of Extras.
     */
    public function getExtras(): string
    {
        return $this->extras;
    }

    /**
     * Set the value of Category.
     *
     * @param string $category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of Category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Set the value of Infokeys.
     *
     * @param string $infokeys
     *
     * @return self
     */
    public function setInfokeys($infokeys)
    {
        $this->infokeys = $infokeys;

        return $this;
    }

    /**
     * Get the value of Infokeys.
     */
    public function getInfokeys(): string
    {
        return $this->infokeys;
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
     * Set the value of Download.
     *
     * @param string $download
     *
     * @return self
     */
    public function setDownload($download)
    {
        $this->download = $download;

        return $this;
    }

    /**
     * Get the value of Download.
     */
    public function getDownload(): string
    {
        return $this->download;
    }
}
