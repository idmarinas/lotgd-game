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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Companions.
 *
 * @ORM\Table(name="companions")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\CompanionsRepository")
 * @Gedmo\TranslationEntity(class="Lotgd\Core\Entity\CompanionsTranslation")
 */
class Companions implements Translatable
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
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="category", type="string", length=50, nullable=false)
     */
    private $category = '';

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description = '';

    /**
     * @var int
     *
     * @ORM\Column(name="attack", type="smallint", nullable=false, options={"default": 1, "unsigned": true})
     *
     * @Assert\Range(
     *     min=1,
     *     max=65535
     * )
     * @Assert\DivisibleBy(1)
     */
    private $attack = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="attackperlevel", type="smallint", nullable=false, options={"default": 0, "unsigned": true})
     *
     * @Assert\Range(
     *     min=0,
     *     max=65535
     * )
     * @Assert\DivisibleBy(1)
     */
    private $attackperlevel = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="defense", type="smallint", nullable=false, options={"default": 1, "unsigned": true})
     *
     * @Assert\Range(
     *     min=1,
     *     max=65535
     * )
     * @Assert\DivisibleBy(1)
     */
    private $defense = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="defenseperlevel", type="smallint", nullable=false, options={"default": 0, "unsigned": true})
     *
     * @Assert\Range(
     *     min=0,
     *     max=65535
     * )
     * @Assert\DivisibleBy(1)
     */
    private $defenseperlevel = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="maxhitpoints", type="smallint", nullable=false, options={"default": 10, "unsigned": true})
     *
     * @Assert\Range(
     *     min=0,
     *     max=65535
     * )
     * @Assert\DivisibleBy(1)
     */
    private $maxhitpoints = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="maxhitpointsperlevel", type="smallint", nullable=false, options={"default": 10, "unsigned": true})
     *
     * @Assert\Range(
     *     min=0,
     *     max=65535
     * )
     * @Assert\DivisibleBy(1)
     */
    private $maxhitpointsperlevel = 10;

    /**
     * @var array
     *
     * @ORM\Column(name="abilities", type="array")
     */
    private $abilities = [];

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
    private $companionactive = true;

    /**
     * @var int
     *
     * @ORM\Column(name="companioncostdks", type="integer", nullable=false, options={"unsigned": true}, options={"default": 0})
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
     */
    private $companioncostdks = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="companioncostgems", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
     */
    private $companioncostgems = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="companioncostgold", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
     */
    private $companioncostgold = 0;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="jointext", type="text", length=65535, nullable=false)
     */
    private $jointext = '';

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="dyingtext", type="string", length=255, nullable=false)
     */
    private $dyingtext = '';

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
     * @ORM\OneToMany(targetEntity="CompanionsTranslation", mappedBy="object", cascade={"all"})
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getCompanionid();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(CompanionsTranslation $t): void
    {
        if ( ! $this->translations->contains($t))
        {
            $t->setObject($this);
            $this->translations->add($t);
        }
    }

    /**
     * Set the value of Companionid.
     *
     * @param int $companionid
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
    public function getCompanionid(): ?int
    {
        return $this->companionid;
    }

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
     * Set the value of Category.
     *
     * @param string $category
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
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Set the value of Description.
     *
     * @param string $description
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
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of Attack.
     *
     * @param int $attack
     *
     * @return self
     */
    public function setAttack($attack)
    {
        $this->attack = (int) $attack;

        return $this;
    }

    /**
     * Get the value of Attack.
     */
    public function getAttack(): int
    {
        return $this->attack;
    }

    /**
     * Set the value of Attackperlevel.
     *
     * @param int $attackperlevel
     *
     * @return self
     */
    public function setAttackperlevel($attackperlevel)
    {
        $this->attackperlevel = (int) $attackperlevel;

        return $this;
    }

    /**
     * Get the value of Attackperlevel.
     */
    public function getAttackperlevel(): int
    {
        return $this->attackperlevel;
    }

    /**
     * Set the value of Defense.
     *
     * @param int $defense
     *
     * @return self
     */
    public function setDefense($defense)
    {
        $this->defense = (int) $defense;

        return $this;
    }

    /**
     * Get the value of Defense.
     */
    public function getDefense(): int
    {
        return $this->defense;
    }

    /**
     * Set the value of Defenseperlevel.
     *
     * @param int $defenseperlevel
     *
     * @return self
     */
    public function setDefenseperlevel($defenseperlevel)
    {
        $this->defenseperlevel = (int) $defenseperlevel;

        return $this;
    }

    /**
     * Get the value of Defenseperlevel.
     */
    public function getDefenseperlevel(): int
    {
        return $this->defenseperlevel;
    }

    /**
     * Set the value of Maxhitpoints.
     *
     * @param int $maxhitpoints
     *
     * @return self
     */
    public function setMaxhitpoints($maxhitpoints)
    {
        $this->maxhitpoints = (int) $maxhitpoints;

        return $this;
    }

    /**
     * Get the value of Maxhitpoints.
     */
    public function getMaxhitpoints(): int
    {
        return $this->maxhitpoints;
    }

    /**
     * Set the value of Maxhitpointsperlevel.
     *
     * @param int $maxhitpointsperlevel
     *
     * @return self
     */
    public function setMaxhitpointsperlevel($maxhitpointsperlevel)
    {
        $this->maxhitpointsperlevel = (int) $maxhitpointsperlevel;

        return $this;
    }

    /**
     * Get the value of Maxhitpointsperlevel.
     */
    public function getMaxhitpointsperlevel(): int
    {
        return $this->maxhitpointsperlevel;
    }

    /**
     * Set the value of Abilities.
     *
     * @return self
     */
    public function setAbilities(array $abilities)
    {
        $this->abilities = $abilities;

        return $this;
    }

    /**
     * Get the value of Abilities.
     *
     * @return array
     */
    public function getAbilities()
    {
        if (\is_string($this->abilities))
        {
            $this->abilities = unserialize($this->abilities);
        }

        return $this->abilities;
    }

    /**
     * Set the value of Cannotdie.
     *
     * @param bool $cannotdie
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
     */
    public function getCannotdie(): bool
    {
        return $this->cannotdie;
    }

    /**
     * Set the value of Cannotbehealed.
     *
     * @param bool $cannotbehealed
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
     */
    public function getCannotbehealed(): bool
    {
        return $this->cannotbehealed;
    }

    /**
     * Set the value of Companionlocation.
     *
     * @param string $companionlocation
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
     */
    public function getCompanionlocation(): string
    {
        return $this->companionlocation;
    }

    /**
     * Set the value of Companionactive.
     *
     * @param bool $companionactive
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
     */
    public function getCompanionactive(): bool
    {
        return $this->companionactive;
    }

    /**
     * Set the value of Companioncostdks.
     *
     * @param int $companioncostdks
     *
     * @return self
     */
    public function setCompanioncostdks($companioncostdks)
    {
        $this->companioncostdks = (int) $companioncostdks;

        return $this;
    }

    /**
     * Get the value of Companioncostdks.
     */
    public function getCompanioncostdks(): int
    {
        return $this->companioncostdks;
    }

    /**
     * Set the value of Companioncostgems.
     *
     * @param int $companioncostgems
     *
     * @return self
     */
    public function setCompanioncostgems($companioncostgems)
    {
        $this->companioncostgems = (int) $companioncostgems;

        return $this;
    }

    /**
     * Get the value of Companioncostgems.
     */
    public function getCompanioncostgems(): int
    {
        return $this->companioncostgems;
    }

    /**
     * Set the value of Companioncostgold.
     *
     * @param int $companioncostgold
     *
     * @return self
     */
    public function setCompanioncostgold($companioncostgold)
    {
        $this->companioncostgold = (int) $companioncostgold;

        return $this;
    }

    /**
     * Get the value of Companioncostgold.
     */
    public function getCompanioncostgold(): int
    {
        return $this->companioncostgold;
    }

    /**
     * Set the value of Jointext.
     *
     * @param string $jointext
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
     */
    public function getJointext(): string
    {
        return $this->jointext;
    }

    /**
     * Set the value of Dyingtext.
     *
     * @param string $dyingtext
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
     */
    public function getDyingtext(): string
    {
        return $this->dyingtext;
    }

    /**
     * Set the value of Allowinshades.
     *
     * @param bool $allowinshades
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
     */
    public function getAllowinshades(): bool
    {
        return $this->allowinshades;
    }

    /**
     * Set the value of Allowinpvp.
     *
     * @param bool $allowinpvp
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
     */
    public function getAllowinpvp(): bool
    {
        return $this->allowinpvp;
    }

    /**
     * Set the value of Allowintrain.
     *
     * @param bool $allowintrain
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
     */
    public function getAllowintrain(): bool
    {
        return $this->allowintrain;
    }
}
