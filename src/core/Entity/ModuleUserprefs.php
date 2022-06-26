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
 * ModuleUserprefs.
 *
 * @ORM\Table(name="module_userprefs",
 *     indexes={
 *         @ORM\Index(name="modulename", columns={"modulename", "userid"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\ModuleUserprefsRepository")
 */
class ModuleUserprefs
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
     * @ORM\Column(name="setting", type="string", length=50)
     * @ORM\Column(name="setting", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $setting;

    /**
     * @var int|null
     *
     * @ORM\Column(name="userid", type="integer", options={"unsigned"=true, "default"="0"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userid = 0;

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
     * Set the value of Userid.
     *
     * @param int $userid
     *
     * @return self
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get the value of Userid.
     */
    public function getUserid(): int
    {
        return $this->userid;
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
