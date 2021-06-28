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
 * Armor.
 *
 * @ORM\Table(name="armor")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\ArmorRepository")
 * @Gedmo\TranslationEntity(class="Lotgd\Core\Entity\ArmorTranslation")
 */
class Armor implements Translatable
{
    /**
     * @var int
     *
     * @ORM\Column(name="armorid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $armorid;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="armorname", type="string", length=128, nullable=true)
     */
    private $armorname;

    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer", nullable=false, options={"unsigned": true})
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
     */
    private $value = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="defense", type="smallint", nullable=false, options={"unsigned": true, "default": "1"})
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
     * @ORM\Column(name="level", type="smallint", nullable=false, options={"unsigned": true})
     *
     * @Assert\Range(
     *     min=0,
     *     max=65535
     * )
     * @Assert\DivisibleBy(1)
     */
    private $level = 0;

    /**
     * @ORM\OneToMany(targetEntity="ArmorTranslation", mappedBy="object", cascade={"all"})
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getArmorid();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(ArmorTranslation $t): void
    {
        if ( ! $this->translations->contains($t))
        {
            $t->setObject($this);
            $this->translations->add($t);
        }
    }

    /**
     * Set the value of Armorid.
     *
     * @param int $armorid
     *
     * @return self
     */
    public function setArmorid($armorid)
    {
        $this->armorid = $armorid;

        return $this;
    }

    /**
     * Get the value of Armorid.
     *
     * @return int
     */
    public function getArmorid(): ?int
    {
        return $this->armorid;
    }

    /**
     * Set the value of Armorname.
     *
     * @param string $armorname
     *
     * @return self
     */
    public function setArmorname(string $armorname)
    {
        $this->armorname = $armorname;

        return $this;
    }

    /**
     * Get the value of Armorname.
     */
    public function getArmorname(): string
    {
        return $this->armorname;
    }

    /**
     * Set the value of Value.
     *
     * @param int $value
     *
     * @return self
     */
    public function setValue(int $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of Value.
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Set the value of Defense.
     *
     * @param int $defense
     *
     * @return self
     */
    public function setDefense(int $defense)
    {
        $this->defense = $defense;

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
     * Set the value of Level.
     *
     * @param int $level
     *
     * @return self
     */
    public function setLevel(int $level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get the value of Level.
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}
