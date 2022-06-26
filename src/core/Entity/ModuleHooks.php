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
 * ModuleHooks.
 *
 * @ORM\Table(name="module_hooks",
 *     indexes={
 *         @ORM\Index(name="location", columns={"location"})
 *     }
 * )
 * @ORM\Entity
 */
class ModuleHooks
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
     * @ORM\Column(name="location", type="string", length=50)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(name="function", type="string", length=50)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $function;

    /**
     * @var string|null
     *
     * @ORM\Column(name="whenactive", type="text", length=65535)
     */
    private $whenactive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="priority", type="integer", options={"default"="50"})
     */
    private $priority = 50;

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
     * Set the value of Location.
     *
     * @param string $location
     *
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the value of Location.
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Set the value of Function.
     *
     * @param string $function
     *
     * @return self
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * Get the value of Function.
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * Set the value of Whenactive.
     *
     * @param string $whenactive
     *
     * @return self
     */
    public function setWhenactive($whenactive)
    {
        $this->whenactive = $whenactive;

        return $this;
    }

    /**
     * Get the value of Whenactive.
     */
    public function getWhenactive(): string
    {
        return $this->whenactive;
    }

    /**
     * Set the value of Priority.
     *
     * @param int $priority
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
    public function getPriority(): int
    {
        return $this->priority;
    }
}
