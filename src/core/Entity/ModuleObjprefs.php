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
 * ModuleObjprefs.
 *
 * @ORM\Table(name="module_objprefs")
 * @ORM\Entity
 */
class ModuleObjprefs
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
     * @ORM\Column(name="objtype", type="string", length=50)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $objtype;

    /**
     * @var string|null
     *
     * @ORM\Column(name="setting", type="string", length=50)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $setting;

    /**
     * @var int|null
     *
     * @ORM\Column(name="objid", type="integer", options={"unsigned"=true, "default"="0"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $objid = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="text", length=65535)
     */
    private $value;

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
     * Set the value of Objtype.
     *
     * @param string $objtype
     *
     * @return self
     */
    public function setObjtype($objtype)
    {
        $this->objtype = $objtype;

        return $this;
    }

    /**
     * Get the value of Objtype.
     */
    public function getObjtype(): string
    {
        return $this->objtype;
    }

    /**
     * Set the value of Setting.
     *
     * @param string $setting
     *
     * @return self
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * Get the value of Setting.
     */
    public function getSetting(): string
    {
        return $this->setting;
    }

    /**
     * Set the value of Objid.
     *
     * @param int $objid
     *
     * @return self
     */
    public function setObjid($objid)
    {
        $this->objid = $objid;

        return $this;
    }

    /**
     * Get the value of Objid.
     */
    public function getObjid(): int
    {
        return $this->objid;
    }

    /**
     * Set the value of Value.
     *
     * @param string $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of Value.
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
