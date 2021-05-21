<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\AvatarBundle\Entity\Avatar;

use Doctrine\ORM\Mapping as ORM;

trait Attributes
{
    /**
     * @ORM\Column(type="integer", options={"default": 10, "unsigned": true})
     */
    protected $strength = 10;

    /**
     * @ORM\Column(type="integer", options={"default": 10, "unsigned": true})
     */
    protected $dexterity = 10;

    /**
     * @ORM\Column(type="integer", options={"default": 10, "unsigned": true})
     */
    protected $intelligence = 10;

    /**
     * @ORM\Column(name="constitution", type="integer", options={"default": 10, "unsigned": true})
     */
    protected $constitution = 10;

    /**
     * @ORM\Column(name="wisdom", type="integer", options={"default": 10, "unsigned": true})
     */
    protected $wisdom = 10;

    /**
     * Set the value of Strength.
     *
     * @param int $strength
     *
     * @return self
     */
    public function setStrength($strength)
    {
        $this->strength = (int) $strength;

        return $this;
    }

    /**
     * Get the value of Strength.
     */
    public function getStrength(): int
    {
        return $this->strength;
    }

    /**
     * Set the value of Dexterity.
     *
     * @param int $dexterity
     *
     * @return self
     */
    public function setDexterity($dexterity)
    {
        $this->dexterity = (int) $dexterity;

        return $this;
    }

    /**
     * Get the value of Dexterity.
     */
    public function getDexterity(): int
    {
        return $this->dexterity;
    }

    /**
     * Set the value of Intelligence.
     *
     * @param int $intelligence
     *
     * @return self
     */
    public function setIntelligence($intelligence)
    {
        $this->intelligence = (int) $intelligence;

        return $this;
    }

    /**
     * Get the value of Intelligence.
     */
    public function getIntelligence(): int
    {
        return $this->intelligence;
    }

    /**
     * Set the value of Constitution.
     *
     * @param int $constitution
     *
     * @return self
     */
    public function setConstitution($constitution)
    {
        $this->constitution = (int) $constitution;

        return $this;
    }

    /**
     * Get the value of Constitution.
     */
    public function getConstitution(): int
    {
        return $this->constitution;
    }

    /**
     * Set the value of Wisdom.
     *
     * @param int $wisdom
     *
     * @return self
     */
    public function setWisdom($wisdom)
    {
        $this->wisdom = (int) $wisdom;

        return $this;
    }

    /**
     * Get the value of Wisdom.
     */
    public function getWisdom(): int
    {
        return $this->wisdom;
    }
}
