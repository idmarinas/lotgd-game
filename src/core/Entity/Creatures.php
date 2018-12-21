<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Creatures.
 *
 * @ORM\Table(name="creatures", indexes={@ORM\Index(name="creaturecategory", columns={"creaturecategory"}), @ORM\Index(name="creaturelevel", columns={"creaturelevel"})})
 * @ORM\Entity
 */
class Creatures
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
     * @var string
     *
     * @ORM\Column(name="creaturecategory", type="string", length=50, nullable=true)
     */
    private $creaturecategory;

    /**
     * @var string
     *
     * @ORM\Column(name="creatureimage", type="string", length=250, nullable=false)
     */
    private $creatureimage;

    /**
     * @var string
     *
     * @ORM\Column(name="creaturedescription", type="text", length=65535, nullable=false)
     */
    private $creaturedescription;

    /**
     * @var string
     *
     * @ORM\Column(name="creatureweapon", type="string", length=50, nullable=true)
     */
    private $creatureweapon;

    /**
     * @var string
     *
     * @ORM\Column(name="creaturegoldbonus", type="decimal", precision=4, scale=2, nullable=false)
     */
    private $creaturegoldbonus = '1.00';

    /**
     * @var string
     *
     * @ORM\Column(name="creatureattackbonus", type="decimal", precision=4, scale=2, nullable=false)
     */
    private $creatureattackbonus = '1.00';

    /**
     * @var string
     *
     * @ORM\Column(name="creaturedefensebonus", type="decimal", precision=4, scale=2, nullable=false)
     */
    private $creaturedefensebonus = '1.00';

    /**
     * @var string
     *
     * @ORM\Column(name="creaturehealthbonus", type="decimal", precision=4, scale=2, nullable=false)
     */
    private $creaturehealthbonus = '1.00';

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
     * @var string
     *
     * @ORM\Column(name="creatureaiscript", type="text", length=65535, nullable=true)
     */
    private $creatureaiscript;

    /**
     * @var string
     *
     * @ORM\Column(name="createdby", type="string", length=50, nullable=true)
     */
    private $createdby;

    /**
     * @var bool
     *
     * @ORM\Column(name="forest", type="boolean", nullable=false)
     */
    private $forest = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="graveyard", type="boolean", nullable=false)
     */
    private $graveyard = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="creaturelevel", type="integer", nullable=true)
     */
    private $creaturelevel;

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
     * @ORM\Column(name="oldcreatureexp", type="integer", nullable=true)
     */
    private $oldcreatureexp;

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
     * Set the value of Creaturecategory.
     *
     * @param string creaturecategory
     *
     * @return self
     */
    public function setCreaturecategory($creaturecategory)
    {
        $this->creaturecategory = $creaturecategory;

        return $this;
    }

    /**
     * Get the value of Creaturecategory.
     *
     * @return string
     */
    public function getCreaturecategory(): string
    {
        return $this->creaturecategory;
    }

    /**
     * Set the value of Creatureimage.
     *
     * @param string creatureimage
     *
     * @return self
     */
    public function setCreatureimage($creatureimage)
    {
        $this->creatureimage = $creatureimage;

        return $this;
    }

    /**
     * Get the value of Creatureimage.
     *
     * @return string
     */
    public function getCreatureimage(): string
    {
        return $this->creatureimage;
    }

    /**
     * Set the value of Creaturedescription.
     *
     * @param string creaturedescription
     *
     * @return self
     */
    public function setCreaturedescription($creaturedescription)
    {
        $this->creaturedescription = $creaturedescription;

        return $this;
    }

    /**
     * Get the value of Creaturedescription.
     *
     * @return string
     */
    public function getCreaturedescription(): string
    {
        return $this->creaturedescription;
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
     * Set the value of Creaturegoldbonus.
     *
     * @param string creaturegoldbonus
     *
     * @return self
     */
    public function setCreaturegoldbonus($creaturegoldbonus)
    {
        $this->creaturegoldbonus = $creaturegoldbonus;

        return $this;
    }

    /**
     * Get the value of Creaturegoldbonus.
     *
     * @return string
     */
    public function getCreaturegoldbonus(): string
    {
        return $this->creaturegoldbonus;
    }

    /**
     * Set the value of Creatureattackbonus.
     *
     * @param string creatureattackbonus
     *
     * @return self
     */
    public function setCreatureattackbonus($creatureattackbonus)
    {
        $this->creatureattackbonus = $creatureattackbonus;

        return $this;
    }

    /**
     * Get the value of Creatureattackbonus.
     *
     * @return string
     */
    public function getCreatureattackbonus(): string
    {
        return $this->creatureattackbonus;
    }

    /**
     * Set the value of Creaturedefensebonus.
     *
     * @param string creaturedefensebonus
     *
     * @return self
     */
    public function setCreaturedefensebonus($creaturedefensebonus)
    {
        $this->creaturedefensebonus = $creaturedefensebonus;

        return $this;
    }

    /**
     * Get the value of Creaturedefensebonus.
     *
     * @return string
     */
    public function getCreaturedefensebonus(): string
    {
        return $this->creaturedefensebonus;
    }

    /**
     * Set the value of Creaturehealthbonus.
     *
     * @param string creaturehealthbonus
     *
     * @return self
     */
    public function setCreaturehealthbonus($creaturehealthbonus)
    {
        $this->creaturehealthbonus = $creaturehealthbonus;

        return $this;
    }

    /**
     * Get the value of Creaturehealthbonus.
     *
     * @return string
     */
    public function getCreaturehealthbonus(): string
    {
        return $this->creaturehealthbonus;
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
     * Set the value of Creatureaiscript.
     *
     * @param string creatureaiscript
     *
     * @return self
     */
    public function setCreatureaiscript($creatureaiscript)
    {
        $this->creatureaiscript = $creatureaiscript;

        return $this;
    }

    /**
     * Get the value of Creatureaiscript.
     *
     * @return string
     */
    public function getCreatureaiscript(): string
    {
        return $this->creatureaiscript;
    }

    /**
     * Set the value of Createdby.
     *
     * @param string createdby
     *
     * @return self
     */
    public function setCreatedby($createdby)
    {
        $this->createdby = $createdby;

        return $this;
    }

    /**
     * Get the value of Createdby.
     *
     * @return string
     */
    public function getCreatedby(): string
    {
        return $this->createdby;
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
     * Set the value of Oldcreatureexp.
     *
     * @param int oldcreatureexp
     *
     * @return self
     */
    public function setOldcreatureexp($oldcreatureexp)
    {
        $this->oldcreatureexp = $oldcreatureexp;

        return $this;
    }

    /**
     * Get the value of Oldcreatureexp.
     *
     * @return int
     */
    public function getOldcreatureexp(): int
    {
        return $this->oldcreatureexp;
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
