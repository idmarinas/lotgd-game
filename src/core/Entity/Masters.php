<?php

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
     * @ORM\Column(name="creatureid", type="integer", nullable=false)
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
     * @ORM\Column(name="creaturelevel", type="integer", nullable=true)
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
     * @var int
     *
     * @ORM\Column(name="creaturegold", type="integer", nullable=true)
     */
    private $creaturegold;

    /**
     * @var int
     *
     * @ORM\Column(name="creatureexp", type="integer", nullable=true)
     */
    private $creatureexp;

    /**
     * @var int
     *
     * @ORM\Column(name="creaturehealth", type="integer", nullable=true)
     */
    private $creaturehealth;

    /**
     * @var int
     *
     * @ORM\Column(name="creatureattack", type="integer", nullable=true)
     */
    private $creatureattack;

    /**
     * @var int
     *
     * @ORM\Column(name="creaturedefense", type="integer", nullable=true)
     */
    private $creaturedefense;

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

    /**
     * Set the value of Creaturegold.
     *
     * @param int creaturegold
     *
     * @return self
     */
    public function setCreaturegold($creaturegold)
    {
        $this->creaturegold = $creaturegold;

        return $this;
    }

    /**
     * Get the value of Creaturegold.
     *
     * @return int
     */
    public function getCreaturegold(): int
    {
        return $this->creaturegold;
    }

    /**
     * Set the value of Creatureexp.
     *
     * @param int creatureexp
     *
     * @return self
     */
    public function setCreatureexp($creatureexp)
    {
        $this->creatureexp = $creatureexp;

        return $this;
    }

    /**
     * Get the value of Creatureexp.
     *
     * @return int
     */
    public function getCreatureexp(): int
    {
        return $this->creatureexp;
    }

    /**
     * Set the value of Creaturehealth.
     *
     * @param int creaturehealth
     *
     * @return self
     */
    public function setCreaturehealth($creaturehealth)
    {
        $this->creaturehealth = $creaturehealth;

        return $this;
    }

    /**
     * Get the value of Creaturehealth.
     *
     * @return int
     */
    public function getCreaturehealth(): int
    {
        return $this->creaturehealth;
    }

    /**
     * Set the value of Creatureattack.
     *
     * @param int creatureattack
     *
     * @return self
     */
    public function setCreatureattack($creatureattack)
    {
        $this->creatureattack = $creatureattack;

        return $this;
    }

    /**
     * Get the value of Creatureattack.
     *
     * @return int
     */
    public function getCreatureattack(): int
    {
        return $this->creatureattack;
    }

    /**
     * Set the value of Creaturedefense.
     *
     * @param int creaturedefense
     *
     * @return self
     */
    public function setCreaturedefense($creaturedefense)
    {
        $this->creaturedefense = $creaturedefense;

        return $this;
    }

    /**
     * Get the value of Creaturedefense.
     *
     * @return int
     */
    public function getCreaturedefense(): int
    {
        return $this->creaturedefense;
    }
}
