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
 * Whostyping.
 *
 * @ORM\Table(name="whostyping")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\WhostypingRepository")
 */
class Whostyping
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, options={"collation": "utf8_general_ci"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="time", type="integer", options={"unsigned"=true})
     */
    private $time;

    /**
     * @var string|null
     *
     * @ORM\Column(name="section", type="string", length=255)
     */
    private $section;

    /**
     * Set the value of Name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of Name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of Time.
     *
     * @param int $time
     *
     * @return self
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get the value of Time.
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * Set the value of Section.
     *
     * @param string $section
     *
     * @return self
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get the value of Section.
     */
    public function getSection(): string
    {
        return $this->section;
    }
}
