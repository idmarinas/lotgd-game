<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Entity\Avatar;

trait Equipment
{
    /**
     * @var int
     *
     * @ORM\Column(name="weaponvalue", type="integer", nullable=false, options={"default": 0})
     */
    private $weaponvalue = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="armorvalue", type="integer", nullable=false, options={"default": 0})
     */
    private $armorvalue = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="weapon", type="string", length=50, nullable=false, options={"default": "Fists"})
     */
    private $weapon = 'Fists';

    /**
     * @var string
     *
     * @ORM\Column(name="armor", type="string", length=50, nullable=false, options={"default": "T-Shirt"})
     */
    private $armor = 'T-Shirt';

    /**
     * Set the value of Weaponvalue.
     *
     * @param int $weaponvalue
     *
     * @return self
     */
    public function setWeaponvalue($weaponvalue)
    {
        $this->weaponvalue = (int) $weaponvalue;

        return $this;
    }

    /**
     * Get the value of Weaponvalue.
     */
    public function getWeaponvalue(): int
    {
        return $this->weaponvalue;
    }

    /**
     * Set the value of Armorvalue.
     *
     * @param int $armorvalue
     *
     * @return self
     */
    public function setArmorvalue($armorvalue)
    {
        $this->armorvalue = (int) $armorvalue;

        return $this;
    }

    /**
     * Get the value of Armorvalue.
     */
    public function getArmorvalue(): int
    {
        return $this->armorvalue;
    }

    /**
     * Set the value of Weapon.
     *
     * @param string $weapon
     *
     * @return self
     */
    public function setWeapon($weapon)
    {
        $this->weapon = $weapon;

        return $this;
    }

    /**
     * Get the value of Weapon.
     */
    public function getWeapon(): string
    {
        return $this->weapon;
    }

    /**
     * Set the value of Armor.
     *
     * @param string $armor
     *
     * @return self
     */
    public function setArmor($armor)
    {
        $this->armor = $armor;

        return $this;
    }

    /**
     * Get the value of Armor.
     */
    public function getArmor(): string
    {
        return $this->armor;
    }
}
