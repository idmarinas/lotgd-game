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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Armor.
 *
 * @ORM\Table(name="armor")
 * @ORM\Entity(repositoryClass="Lotgd\Bundle\CoreBundle\Repository\ArmorRepository")
 * @Gedmo\TranslationEntity(class="Lotgd\Bundle\CoreBundle\Entity\ArmorTranslation")
 */
class Armor implements TranslatableInterface
{
    use PersonalTranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="armorid", type="integer", options={"unsigned": true})
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
     * @ORM\Column(name="value", type="integer", options={"unsigned": true})
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
     * @ORM\Column(name="defense", type="smallint", options={"unsigned": true, "default": "1"})
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
     * @ORM\Column(name="level", type="smallint", options={"unsigned": true})
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
     *
     * @var \Lotgd\Bundle\CoreBundle\Entity\ArmorTranslation[]|\Doctrine\Common\Collections\Collection<int, \Lotgd\Bundle\CoreBundle\Entity\ArmorTranslation>
     */
    private $translations;

    public function __toString()
    {
        return $this->getArmorname();
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
