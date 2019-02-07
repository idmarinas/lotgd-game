<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Masters.
 *
 * @ORM\Table(name="masters")
 * @ORM\Entity
 */
class Masters
{
    /**
     * @var int
     *
     * @ORM\Column(name="creatureid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $creatureid;

    /**
     * @var string
     *
     * @ORM\Column(name="creaturename", type="string", length=50, nullable=true)
     */
    private $creaturename;

    /**
     * @var int
     *
     * @ORM\Column(name="creaturelevel", type="integer", nullable=true, options={"unsigned": true})
     */
    private $creaturelevel;

    /**
     * @var string
     *
     * @ORM\Column(name="creatureweapon", type="string", length=50, nullable=true)
     */
    private $creatureweapon;

    /**
     * @var string
     *
     * @ORM\Column(name="creaturelose", type="string", length=120, nullable=true)
     */
    private $creaturelose;

    /**
     * @var string
     *
     * @ORM\Column(name="creaturewin", type="string", length=120, nullable=true)
     */
    private $creaturewin;

    /**
     * Set the value of Creatureid.
     *
     * @param int creatureid
     *
     * @return self
     */
    public function setCreatureid($creatureid)
    {
        $this->creatureid = $creatureid;

        return $this;
    }

    /**
     * Get the value of Creatureid.
     *
     * @return int
     */
    public function getCreatureid(): int
    {
        return $this->creatureid;
    }

    /**
     * Set the value of Creaturename.
     *
     * @param string creaturename
     *
     * @return self
     */
    public function setCreaturename($creaturename)
    {
        $this->creaturename = $creaturename;

        return $this;
    }

    /**
     * Get the value of Creaturename.
     *
     * @return string
     */
    public function getCreaturename(): string
    {
        return $this->creaturename;
    }

    /**
     * Set the value of Creaturelevel.
     *
     * @param int creaturelevel
     *
     * @return self
     */
    public function setCreaturelevel($creaturelevel)
    {
        $this->creaturelevel = $creaturelevel;

        return $this;
    }

    /**
     * Get the value of Creaturelevel.
     *
     * @return int
     */
    public function getCreaturelevel(): int
    {
        return $this->creaturelevel;
    }

    /**
     * Set the value of Creatureweapon.
     *
     * @param string creatureweapon
     *
     * @return self
     */
    public function setCreatureweapon($creatureweapon)
    {
        $this->creatureweapon = $creatureweapon;

        return $this;
    }

    /**
     * Get the value of Creatureweapon.
     *
     * @return string
     */
    public function getCreatureweapon(): string
    {
        return $this->creatureweapon;
    }

    /**
     * Set the value of Creaturelose.
     *
     * @param string creaturelose
     *
     * @return self
     */
    public function setCreaturelose($creaturelose)
    {
        $this->creaturelose = $creaturelose;

        return $this;
    }

    /**
     * Get the value of Creaturelose.
     *
     * @return string
     */
    public function getCreaturelose(): string
    {
        return $this->creaturelose;
    }

    /**
     * Set the value of Creaturewin.
     *
     * @param string creaturewin
     *
     * @return self
     */
    public function setCreaturewin($creaturewin)
    {
        $this->creaturewin = $creaturewin;

        return $this;
    }

    /**
     * Get the value of Creaturewin.
     *
     * @return string
     */
    public function getCreaturewin(): string
    {
        return $this->creaturewin;
    }
}
