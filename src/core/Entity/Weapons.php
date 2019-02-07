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
 * Weapons.
 *
 * @ORM\Table(name="weapons")
 * @ORM\Entity
 */
class Weapons
{
    /**
     * @var int
     *
     * @ORM\Column(name="weaponid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $weaponid;

    /**
     * @var string
     *
     * @ORM\Column(name="weaponname", type="string", length=128, nullable=true)
     */
    private $weaponname;

    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer", nullable=false, options={"unsigned": true})
     */
    private $value = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="damage", type="smallint", nullable=false, options={"unsigned": true, "default": "1"})
     */
    private $damage = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", nullable=false, options={"unsigned": true})
     */
    private $level = 0;

    /**
     * Set the value of Weaponid.
     *
     * @param int weaponid
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
     *
     * @return int
     */
    public function getWeaponid(): int
    {
        return $this->weaponid;
    }

    /**
     * Set the value of Weaponname.
     *
     * @param string weaponname
     *
     * @return self
     */
    public function setWeaponname($weaponname)
    {
        $this->weaponname = $weaponname;

        return $this;
    }

    /**
     * Get the value of Weaponname.
     *
     * @return string
     */
    public function getWeaponname(): string
    {
        return $this->weaponname;
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
     * Set the value of Damage.
     *
     * @param int damage
     *
     * @return self
     */
    public function setDamage($damage)
    {
        $this->damage = $damage;

        return $this;
    }

    /**
     * Get the value of Damage.
     *
     * @return int
     */
    public function getDamage(): int
    {
        return $this->damage;
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
