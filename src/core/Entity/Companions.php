<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Companions.
 *
 * @ORM\Table(name="companions")
 * @ORM\Entity
 */
class Companions
{
    /**
     * @var int
     *
     * @ORM\Column(name="companionid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $companionid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=50, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="attack", type="smallint", nullable=false, options={"default": 1, "unsigned": true})
     */
    private $attack = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="attackperlevel", type="smallint", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $attackperlevel = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="defense", type="smallint", nullable=false, options={"default": 1, "unsigned": true})
     */
    private $defense = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="defenseperlevel", type="smallint", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $defenseperlevel = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="maxhitpoints", type="smallint", nullable=false, options={"default": 10, "unsigned": true})
     */
    private $maxhitpoints = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="maxhitpointsperlevel", type="smallint", nullable=false, options={"default": 10, "unsigned": true})
     */
    private $maxhitpointsperlevel = 10;

    /**
     * @var string
     *
     * @ORM\Column(name="abilities", type="array", nullable=false)
     */
    private $abilities;

    /**
     * @var bool
     *
     * @ORM\Column(name="cannotdie", type="boolean", nullable=false, options={"default": 0})
     */
    private $cannotdie = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="cannotbehealed", type="boolean", nullable=false, options={"default": 1})
     */
    private $cannotbehealed = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="companionlocation", type="string", length=25, nullable=false)
     */
    private $companionlocation = 'all';

    /**
     * @var bool
     *
     * @ORM\Column(name="companionactive", type="boolean", nullable=false, options={"default": 1})
     */
    private $companionactive = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="companioncostdks", type="integer", nullable=false, options={"unsigned": true}, options={"default": 0})
     */
    private $companioncostdks = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="companioncostgems", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $companioncostgems = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="companioncostgold", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $companioncostgold = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="jointext", type="text", length=65535, nullable=false)
     */
    private $jointext;

    /**
     * @var string
     *
     * @ORM\Column(name="dyingtext", type="string", length=255, nullable=false)
     */
    private $dyingtext;

    /**
     * @var bool
     *
     * @ORM\Column(name="allowinshades", type="boolean", nullable=false, options={"default": 0})
     */
    private $allowinshades = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="allowinpvp", type="boolean", nullable=false, options={"default": 0})
     */
    private $allowinpvp = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="allowintrain", type="boolean", nullable=false, options={"default": 0})
     */
    private $allowintrain = 0;

    /**
     * Set the value of Companionid.
     *
     * @param int companionid
     *
     * @return self
     */
    public function setCompanionid($companionid)
    {
        $this->companionid = $companionid;

        return $this;
    }

    /**
     * Get the value of Companionid.
     *
     * @return int
     */
    public function getCompanionid(): int
    {
        return $this->companionid;
    }

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
     * Set the value of Category.
     *
     * @param string category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of Category.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Set the value of Description.
     *
     * @param string description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of Description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of Attack.
     *
     * @param int attack
     *
     * @return self
     */
    public function setAttack($attack)
    {
        $this->attack = $attack;

        return $this;
    }

    /**
     * Get the value of Attack.
     *
     * @return int
     */
    public function getAttack(): int
    {
        return $this->attack;
    }

    /**
     * Set the value of Attackperlevel.
     *
     * @param int attackperlevel
     *
     * @return self
     */
    public function setAttackperlevel($attackperlevel)
    {
        $this->attackperlevel = $attackperlevel;

        return $this;
    }

    /**
     * Get the value of Attackperlevel.
     *
     * @return int
     */
    public function getAttackperlevel(): int
    {
        return $this->attackperlevel;
    }

    /**
     * Set the value of Defense.
     *
     * @param int defense
     *
     * @return self
     */
    public function setDefense($defense)
    {
        $this->defense = $defense;

        return $this;
    }

    /**
     * Get the value of Defense.
     *
     * @return int
     */
    public function getDefense(): int
    {
        return $this->defense;
    }

    /**
     * Set the value of Defenseperlevel.
     *
     * @param int defenseperlevel
     *
     * @return self
     */
    public function setDefenseperlevel($defenseperlevel)
    {
        $this->defenseperlevel = $defenseperlevel;

        return $this;
    }

    /**
     * Get the value of Defenseperlevel.
     *
     * @return int
     */
    public function getDefenseperlevel(): int
    {
        return $this->defenseperlevel;
    }

    /**
     * Set the value of Maxhitpoints.
     *
     * @param int maxhitpoints
     *
     * @return self
     */
    public function setMaxhitpoints($maxhitpoints)
    {
        $this->maxhitpoints = $maxhitpoints;

        return $this;
    }

    /**
     * Get the value of Maxhitpoints.
     *
     * @return int
     */
    public function getMaxhitpoints(): int
    {
        return $this->maxhitpoints;
    }

    /**
     * Set the value of Maxhitpointsperlevel.
     *
     * @param int maxhitpointsperlevel
     *
     * @return self
     */
    public function setMaxhitpointsperlevel($maxhitpointsperlevel)
    {
        $this->maxhitpointsperlevel = $maxhitpointsperlevel;

        return $this;
    }

    /**
     * Get the value of Maxhitpointsperlevel.
     *
     * @return int
     */
    public function getMaxhitpointsperlevel(): int
    {
        return $this->maxhitpointsperlevel;
    }

    /**
     * Set the value of Abilities.
     *
     * @param string abilities
     *
     * @return self
     */
    public function setAbilities($abilities)
    {
        $this->abilities = $abilities;

        return $this;
    }

    /**
     * Get the value of Abilities.
     *
     * @return string
     */
    public function getAbilities(): string
    {
        return $this->abilities;
    }

    /**
     * Set the value of Cannotdie.
     *
     * @param bool cannotdie
     *
     * @return self
     */
    public function setCannotdie($cannotdie)
    {
        $this->cannotdie = $cannotdie;

        return $this;
    }

    /**
     * Get the value of Cannotdie.
     *
     * @return bool
     */
    public function getCannotdie(): bool
    {
        return $this->cannotdie;
    }

    /**
     * Set the value of Cannotbehealed.
     *
     * @param bool cannotbehealed
     *
     * @return self
     */
    public function setCannotbehealed($cannotbehealed)
    {
        $this->cannotbehealed = $cannotbehealed;

        return $this;
    }

    /**
     * Get the value of Cannotbehealed.
     *
     * @return bool
     */
    public function getCannotbehealed(): bool
    {
        return $this->cannotbehealed;
    }

    /**
     * Set the value of Companionlocation.
     *
     * @param string companionlocation
     *
     * @return self
     */
    public function setCompanionlocation($companionlocation)
    {
        $this->companionlocation = $companionlocation;

        return $this;
    }

    /**
     * Get the value of Companionlocation.
     *
     * @return string
     */
    public function getCompanionlocation(): string
    {
        return $this->companionlocation;
    }

    /**
     * Set the value of Companionactive.
     *
     * @param bool companionactive
     *
     * @return self
     */
    public function setCompanionactive($companionactive)
    {
        $this->companionactive = $companionactive;

        return $this;
    }

    /**
     * Get the value of Companionactive.
     *
     * @return bool
     */
    public function getCompanionactive(): bool
    {
        return $this->companionactive;
    }

    /**
     * Set the value of Companioncostdks.
     *
     * @param int companioncostdks
     *
     * @return self
     */
    public function setCompanioncostdks($companioncostdks)
    {
        $this->companioncostdks = $companioncostdks;

        return $this;
    }

    /**
     * Get the value of Companioncostdks.
     *
     * @return int
     */
    public function getCompanioncostdks(): int
    {
        return $this->companioncostdks;
    }

    /**
     * Set the value of Companioncostgems.
     *
     * @param int companioncostgems
     *
     * @return self
     */
    public function setCompanioncostgems($companioncostgems)
    {
        $this->companioncostgems = $companioncostgems;

        return $this;
    }

    /**
     * Get the value of Companioncostgems.
     *
     * @return int
     */
    public function getCompanioncostgems(): int
    {
        return $this->companioncostgems;
    }

    /**
     * Set the value of Companioncostgold.
     *
     * @param int companioncostgold
     *
     * @return self
     */
    public function setCompanioncostgold($companioncostgold)
    {
        $this->companioncostgold = $companioncostgold;

        return $this;
    }

    /**
     * Get the value of Companioncostgold.
     *
     * @return int
     */
    public function getCompanioncostgold(): int
    {
        return $this->companioncostgold;
    }

    /**
     * Set the value of Jointext.
     *
     * @param string jointext
     *
     * @return self
     */
    public function setJointext($jointext)
    {
        $this->jointext = $jointext;

        return $this;
    }

    /**
     * Get the value of Jointext.
     *
     * @return string
     */
    public function getJointext(): string
    {
        return $this->jointext;
    }

    /**
     * Set the value of Dyingtext.
     *
     * @param string dyingtext
     *
     * @return self
     */
    public function setDyingtext($dyingtext)
    {
        $this->dyingtext = $dyingtext;

        return $this;
    }

    /**
     * Get the value of Dyingtext.
     *
     * @return string
     */
    public function getDyingtext(): string
    {
        return $this->dyingtext;
    }

    /**
     * Set the value of Allowinshades.
     *
     * @param bool allowinshades
     *
     * @return self
     */
    public function setAllowinshades($allowinshades)
    {
        $this->allowinshades = $allowinshades;

        return $this;
    }

    /**
     * Get the value of Allowinshades.
     *
     * @return bool
     */
    public function getAllowinshades(): bool
    {
        return $this->allowinshades;
    }

    /**
     * Set the value of Allowinpvp.
     *
     * @param bool allowinpvp
     *
     * @return self
     */
    public function setAllowinpvp($allowinpvp)
    {
        $this->allowinpvp = $allowinpvp;

        return $this;
    }

    /**
     * Get the value of Allowinpvp.
     *
     * @return bool
     */
    public function getAllowinpvp(): bool
    {
        return $this->allowinpvp;
    }

    /**
     * Set the value of Allowintrain.
     *
     * @param bool allowintrain
     *
     * @return self
     */
    public function setAllowintrain($allowintrain)
    {
        $this->allowintrain = $allowintrain;

        return $this;
    }

    /**
     * Get the value of Allowintrain.
     *
     * @return bool
     */
    public function getAllowintrain(): bool
    {
        return $this->allowintrain;
    }
}
