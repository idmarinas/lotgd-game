<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armor.
 *
 * @ORM\Table(name="armor")
 * @ORM\Entity
 */
class Armor
{
    /**
     * @var int
     *
     * @ORM\Column(name="armorid", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $armorid;

    /**
     * @var string
     *
     * @ORM\Column(name="armorname", type="string", length=128, nullable=true)
     */
    private $armorname;

    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer", nullable=false, options={"unsigned":true})
     */
    private $value = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="defense", type="smallint", nullable=false, options={"unsigned":true, "default":"1"})
     */
    private $defense = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", nullable=false, options={"unsigned":true})
     */
    private $level = 0;

    /**
     * Set the value of Armorid.
     *
     * @param int armorid
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
    public function getArmorid(): int
    {
        return $this->armorid;
    }

    /**
     * Set the value of Armorname.
     *
     * @param string armorname
     *
     * @return self
     */
    public function setArmorname($armorname)
    {
        $this->armorname = $armorname;

        return $this;
    }

    /**
     * Get the value of Armorname.
     *
     * @return string
     */
    public function getArmorname(): string
    {
        return $this->armorname;
    }

    /**
     * Set the value of Value.
     *
     * @param int value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of Value.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
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
     * Set the value of Level.
     *
     * @param int level
     *
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get the value of Level.
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}
