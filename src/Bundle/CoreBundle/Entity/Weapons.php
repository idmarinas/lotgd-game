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
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Weapons.
 *
 * @ORM\Table(name="weapons")
 * @ORM\Entity(repositoryClass="Lotgd\Bundle\CoreBundle\Repository\WeaponsRepository")
 * @Gedmo\TranslationEntity(class="Lotgd\Bundle\CoreBundle\Entity\WeaponsTranslation")
 */
class Weapons implements TranslatableInterface
{
    use PersonalTranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="weaponid", type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $weaponid;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="weaponname", type="string", length=128, nullable=true)
     */
    private $weaponname;

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
     * @ORM\Column(name="damage", type="smallint", options={"unsigned": true, "default": "1"})
     *
     * @Assert\Range(
     *     min=1,
     *     max=65535
     * )
     * @Assert\DivisibleBy(1)
     */
    private $damage = 1;

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
     * @ORM\OneToMany(targetEntity="WeaponsTranslation", mappedBy="object", cascade={"all"})
     *
     * @var \Lotgd\Bundle\CoreBundle\Entity\WeaponsTranslation[]|\Doctrine\Common\Collections\Collection<int, \Lotgd\Bundle\CoreBundle\Entity\WeaponsTranslation>
     */
    private $translations;

    public function __toString()
    {
        return $this->getWeaponname();
    }

    /**
     * Set the value of Weaponid.
     *
     * @param int $weaponid
     *
     * @return self
     */
    public function setWeaponid($weaponid)
    {
        $this->weaponid = $weaponid;

        return $this;
    }

    /**
     * Get the value of Weaponid.
     */
    public function getWeaponid(): ?int
    {
        return $this->weaponid;
    }

    /**
     * Set the value of Weaponname.
     *
     * @return self
     */
    public function setWeaponname(string $weaponname)
    {
        $this->weaponname = $weaponname;

        return $this;
    }

    /**
     * Get the value of Weaponname.
     */
    public function getWeaponname(): string
    {
        return $this->weaponname;
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
     * Set the value of Damage.
     *
     * @return self
     */
    public function setDamage(int $damage)
    {
        $this->damage = $damage;

        return $this;
    }

    /**
     * Get the value of Damage.
     */
    public function getDamage(): int
    {
        return $this->damage;
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
