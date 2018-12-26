<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleUserprefs.
 *
 * @ORM\Table(name="module_userprefs",
 *      indexes={
 *          @ORM\Index(name="modulename", columns={"modulename", "userid"})
 *      }
 * )
 * @ORM\Entity
 */
class ModuleUserprefs
{
    /**
     * @var string
     *
     * @ORM\Column(name="modulename", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $modulename;

    /**
     * @var string
     *
     * @ORM\Column(name="setting", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $setting;

    /**
     * @var int
     *
     * @ORM\Column(name="userid", type="integer", nullable=false, options={"unsigned":true, "default":"0"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=false)
     */
    private $value;

    /**
     * Set the value of Modulename.
     *
     * @param string modulename
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
     *
     * @return string
     */
    public function getModulename(): string
    {
        return $this->modulename;
    }

    /**
     * Set the value of Setting.
     *
     * @param string setting
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
     *
     * @return string
     */
    public function getSetting(): string
    {
        return $this->setting;
    }

    /**
     * Set the value of Userid.
     *
     * @param int userid
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
     *
     * @return int
     */
    public function getUserid(): int
    {
        return $this->userid;
    }

    /**
     * Set the value of Value.
     *
     * @param string value
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
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
