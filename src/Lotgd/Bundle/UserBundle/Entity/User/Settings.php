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

namespace Lotgd\Bundle\UserBundle\Entity\User;

use Doctrine\Common\Collections\Collection;
use Lotgd\Bundle\SettingsBundle\Entity\Setting;

trait Settings
{
    /**
     * @ORM\OneToMany(targetEntity=Setting::class, mappedBy="user")
     */
    private $settings;

    /**
     * @return Collection|Setting[]
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function addSetting(Setting $setting): self
    {
        if ( ! $this->settings->contains($setting))
        {
            $this->settings[] = $setting;
            $setting->setUser($this);
        }

        return $this;
    }

    public function removeSetting(Setting $setting): self
    {
        if ($this->settings->removeElement($setting) && $setting->getUser() === $this)
        {
            // set the owning side to null (unless already changed)
            $setting->setUser(null);
        }

        return $this;
    }
}
