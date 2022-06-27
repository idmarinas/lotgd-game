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

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings.
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\SettingsRepository")
 */
class Settings
{
    /**
     *
     * @ORM\Column(name="setting", type="string", length=25)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private ?string $setting = null;

    /**
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private ?string $value = null;

    /**
     * Set the value of Setting.
     *
     * @param string $setting
     *
     * @return self
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * Get the value of Setting.
     */
    public function getSetting(): string
    {
        return $this->setting;
    }

    /**
     * Set the value of Value.
     *
     * @param string $value
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
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
