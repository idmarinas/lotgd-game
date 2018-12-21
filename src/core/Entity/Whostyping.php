<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Whostyping.
 *
 * @ORM\Table(name="whostyping")
 * @ORM\Entity
 */
class Whostyping
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="time", type="integer", nullable=false)
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=255, nullable=false)
     */
    private $section;

    /**
     * Set the value of Name.
     *
     * @param string name
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
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of Time.
     *
     * @param int time
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
     *
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * Set the value of Section.
     *
     * @param string section
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
     *
     * @return string
     */
    public function getSection(): string
    {
        return $this->section;
    }
}
