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

namespace Lotgd\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creatures.
 *
 * @ORM\Table(name="creatures",
 *     indexes={
 *         @ORM\Index(name="creaturecategory", columns={"creaturecategory"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Bundle\CoreBundle\EntityRepository\CreaturesRepository")
 * @Gedmo\TranslationEntity(class="Lotgd\Bundle\CoreBundle\Entity\CreaturesTranslation")
 */
class Creatures implements Translatable
{
    /**
     * @var int
     *
     * @ORM\Column(name="creatureid", type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $creatureid;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creaturename", type="string", length=50, nullable=true)
     *
     * @Assert\Length(
     *     min=1,
     *     max=50
     * )
     */
    private $creaturename;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creaturecategory", type="string", length=50, nullable=true)
     *
     * @Assert\Length(
     *     min=0,
     *     max=50,
     *     allowEmptyString=true
     * )
     */
    private $creaturecategory = '';

    /**
     * @var string
     *
     * @ORM\Column(name="creatureimage", type="string", length=250)
     *
     * @Assert\Length(
     *     min=0,
     *     max=250,
     *     allowEmptyString=true
     * )
     */
    private $creatureimage = '';

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creaturedescription", type="text", length=65535)
     *
     * @Assert\Length(
     *     min=0,
     *     max=65535,
     *     allowEmptyString=true
     * )
     */
    private $creaturedescription = '';

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creatureweapon", type="string", length=50, nullable=true)
     */
    private $creatureweapon = '';

    /**
     * @var string
     *
     * @ORM\Column(name="creaturegoldbonus", type="decimal", precision=4, scale=2)
     *
     * @Assert\Range(
     *     min=0,
     *     max=99.99
     * )
     * @Assert\DivisibleBy(0.01)
     */
    private $creaturegoldbonus = '1.00';

    /**
     * @var string
     *
     * @ORM\Column(name="creatureattackbonus", type="decimal", precision=4, scale=2)
     *
     * @Assert\Range(
     *     min=0,
     *     max=99.99
     * )
     * @Assert\DivisibleBy(0.01)
     */
    private $creatureattackbonus = '1.00';

    /**
     * @var string
     *
     * @ORM\Column(name="creaturedefensebonus", type="decimal", precision=4, scale=2)
     *
     * @Assert\Range(
     *     min=0,
     *     max=99.99
     * )
     * @Assert\DivisibleBy(0.01)
     */
    private $creaturedefensebonus = '1.00';

    /**
     * @var string
     *
     * @ORM\Column(name="creaturehealthbonus", type="decimal", precision=4, scale=2)
     *
     * @Assert\Range(
     *     min=0,
     *     max=99.99
     * )
     * @Assert\DivisibleBy(0.01)
     */
    private $creaturehealthbonus = '1.00';

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creaturelose", type="string", length=120, nullable=true)
     *
     * @Assert\Length(
     *     min=0,
     *     max=120,
     *     allowEmptyString=true
     * )
     */
    private $creaturelose = '';

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="creaturewin", type="string", length=120, nullable=true)
     *
     * @Assert\Length(
     *     min=0,
     *     max=120,
     *     allowEmptyString=true
     * )
     */
    private $creaturewin = '';

    /**
     * @var string
     *
     * @ORM\Column(name="creatureaiscript", type="text", length=65535, nullable=true)
     *
     * @Assert\Length(
     *     min=0,
     *     max=65535,
     *     allowEmptyString=true
     * )
     */
    private $creatureaiscript = '';

    /**
     * @var string
     *
     * @ORM\Column(name="createdby", type="string", length=50, nullable=true)
     *
     * @Assert\Length(
     *     min=0,
     *     max=50
     * )
     */
    private $createdby = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="forest", type="boolean", nullable=false, options={"default": 0})
     */
    private $forest = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="graveyard", type="boolean", nullable=false, options={"default": 0})
     */
    private $graveyard = 0;

    /**
     * @ORM\OneToMany(targetEntity="CreaturesTranslation", mappedBy="object", cascade={"all"})
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getCreatureid();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(CreaturesTranslation $t): void
    {
        if ( ! $this->translations->contains($t))
        {
            $t->setObject($this);
            $this->translations->add($t);
        }
    }

    /**
     * Set the value of Creatureid.
     *
     * @param int $creatureid
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
     */
    public function getCreatureid(): ?int
    {
        return $this->creatureid;
    }

    /**
     * Temporal alias.
     * In future updates all table IDs will have the field name as `id`.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getCreatureid();
    }

    /**
     * Set the value of Creaturename.
     *
     * @param string $creaturename
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
     */
    public function getCreaturename(): string
    {
        return $this->creaturename;
    }

    /**
     * Set the value of Creaturecategory.
     *
     * @param string $creaturecategory
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
     */
    public function getCreaturecategory(): ?string
    {
        return $this->creaturecategory;
    }

    /**
     * Set the value of Creatureimage.
     *
     * @param string $creatureimage
     *
     * @return self
     */
    public function setCreatureimage($creatureimage)
    {
        $this->creatureimage = (string) $creatureimage;

        return $this;
    }

    /**
     * Get the value of Creatureimage.
     */
    public function getCreatureimage(): string
    {
        return (string) $this->creatureimage;
    }

    /**
     * Set the value of Creaturedescription.
     *
     * @param string $creaturedescription
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
     */
    public function getCreaturedescription(): string
    {
        return $this->creaturedescription;
    }

    /**
     * Set the value of Creatureweapon.
     *
     * @param string $creatureweapon
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
     */
    public function getCreatureweapon(): string
    {
        return $this->creatureweapon;
    }

    /**
     * Set the value of Creaturegoldbonus.
     *
     * @param string $creaturegoldbonus
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
     */
    public function getCreaturegoldbonus(): string
    {
        return $this->creaturegoldbonus;
    }

    /**
     * Set the value of Creatureattackbonus.
     *
     * @param string $creatureattackbonus
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
     */
    public function getCreatureattackbonus(): string
    {
        return $this->creatureattackbonus;
    }

    /**
     * Set the value of Creaturedefensebonus.
     *
     * @param string $creaturedefensebonus
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
     */
    public function getCreaturedefensebonus(): string
    {
        return $this->creaturedefensebonus;
    }

    /**
     * Set the value of Creaturehealthbonus.
     *
     * @param string $creaturehealthbonus
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
     */
    public function getCreaturehealthbonus(): string
    {
        return $this->creaturehealthbonus;
    }

    /**
     * Set the value of Creaturelose.
     *
     * @param string $creaturelose
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
     */
    public function getCreaturelose(): string
    {
        return $this->creaturelose;
    }

    /**
     * Set the value of Creaturewin.
     *
     * @param string $creaturewin
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
     */
    public function getCreaturewin(): ?string
    {
        return $this->creaturewin;
    }

    /**
     * Set the value of Creatureaiscript.
     *
     * @param string $creatureaiscript
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
     */
    public function getCreatureaiscript(): ?string
    {
        return $this->creatureaiscript;
    }

    /**
     * Set the value of Createdby.
     *
     * @param string $createdby
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
     */
    public function getCreatedby(): ?string
    {
        return $this->createdby;
    }

    /**
     * Set the value of Forest.
     *
     * @param bool $forest
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
     */
    public function getForest(): bool
    {
        return $this->forest;
    }

    /**
     * Set the value of Graveyard.
     *
     * @param bool $graveyard
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
     */
    public function getGraveyard(): bool
    {
        return $this->graveyard;
    }
}
