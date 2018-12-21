<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Taunts.
 *
 * @ORM\Table(name="taunts")
 * @ORM\Entity
 */
class Taunts
{
    /**
     * @var int
     *
     * @ORM\Column(name="tauntid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tauntid;

    /**
     * @var string
     *
     * @ORM\Column(name="taunt", type="text", length=65535, nullable=true)
     */
    private $taunt;

    /**
     * @var string
     *
     * @ORM\Column(name="editor", type="string", length=50, nullable=true)
     */
    private $editor;

    /**
     * Set the value of Tauntid.
     *
     * @param int tauntid
     *
     * @return self
     */
    public function setTauntid($tauntid)
    {
        $this->tauntid = $tauntid;

        return $this;
    }

    /**
     * Get the value of Tauntid.
     *
     * @return int
     */
    public function getTauntid(): int
    {
        return $this->tauntid;
    }

    /**
     * Set the value of Taunt.
     *
     * @param string taunt
     *
     * @return self
     */
    public function setTaunt($taunt)
    {
        $this->taunt = $taunt;

        return $this;
    }

    /**
     * Get the value of Taunt.
     *
     * @return string
     */
    public function getTaunt(): string
    {
        return $this->taunt;
    }

    /**
     * Set the value of Editor.
     *
     * @param string editor
     *
     * @return self
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * Get the value of Editor.
     *
     * @return string
     */
    public function getEditor(): string
    {
        return $this->editor;
    }
}
