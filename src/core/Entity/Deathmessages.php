<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deathmessages.
 *
 * @ORM\Table(name="deathmessages",
 *      indexes={
 *          @ORM\Index(name="forest", columns={"forest"}),
 *          @ORM\Index(name="graveyard", columns={"graveyard"}),
 *          @ORM\Index(name="taunt", columns={"taunt"})
 *      }
 * )
 * @ORM\Entity
 */
class Deathmessages
{
    /**
     * @var int
     *
     * @ORM\Column(name="deathmessageid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $deathmessageid;

    /**
     * @var string
     *
     * @ORM\Column(name="deathmessage", type="string", length=500, nullable=true)
     */
    private $deathmessage;

    /**
     * @var bool
     *
     * @ORM\Column(name="forest", type="boolean", nullable=false, options={"default":1})
     */
    private $forest = 1;

    /**
     * @var bool
     *
     * @ORM\Column(name="graveyard", type="boolean", nullable=false, options={"default":0})
     */
    private $graveyard = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="taunt", type="boolean", nullable=false, options={"default":1})
     */
    private $taunt = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="editor", type="string", length=50, nullable=true)
     */
    private $editor;

    /**
     * Set the value of Deathmessageid.
     *
     * @param int deathmessageid
     *
     * @return self
     */
    public function setDeathmessageid($deathmessageid)
    {
        $this->deathmessageid = $deathmessageid;

        return $this;
    }

    /**
     * Get the value of Deathmessageid.
     *
     * @return int
     */
    public function getDeathmessageid(): int
    {
        return $this->deathmessageid;
    }

    /**
     * Set the value of Deathmessage.
     *
     * @param string deathmessage
     *
     * @return self
     */
    public function setDeathmessage($deathmessage)
    {
        $this->deathmessage = $deathmessage;

        return $this;
    }

    /**
     * Get the value of Deathmessage.
     *
     * @return string
     */
    public function getDeathmessage(): string
    {
        return $this->deathmessage;
    }

    /**
     * Set the value of Forest.
     *
     * @param bool forest
     *
     * @return self
     */
    public function setForest($forest)
    {
        $this->forest = $forest;

        return $this;
    }

    /**
     * Get the value of Forest.
     *
     * @return bool
     */
    public function getForest(): bool
    {
        return $this->forest;
    }

    /**
     * Set the value of Graveyard.
     *
     * @param bool graveyard
     *
     * @return self
     */
    public function setGraveyard($graveyard)
    {
        $this->graveyard = $graveyard;

        return $this;
    }

    /**
     * Get the value of Graveyard.
     *
     * @return bool
     */
    public function getGraveyard(): bool
    {
        return $this->graveyard;
    }

    /**
     * Set the value of Taunt.
     *
     * @param bool taunt
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
     * @return bool
     */
    public function getTaunt(): bool
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
