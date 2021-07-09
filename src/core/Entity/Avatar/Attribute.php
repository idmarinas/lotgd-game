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

trait Attribute
{
    /**
     * @var int
     *
     * @ORM\Column(name="strength", type="integer", nullable=false, options={"default": 10, "unsigned": true})
     */
    private $strength = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="dexterity", type="integer", nullable=false, options={"default": 10, "unsigned": true})
     */
    private $dexterity = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="intelligence", type="integer", nullable=false, options={"default": 10, "unsigned": true})
     */
    private $intelligence = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="constitution", type="integer", nullable=false, options={"default": 10, "unsigned": true})
     */
    private $constitution = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="wisdom", type="integer", nullable=false, options={"default": 10, "unsigned": true})
     */
    private $wisdom = 10;

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
