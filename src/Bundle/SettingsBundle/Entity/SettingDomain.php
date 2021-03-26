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

namespace Lotgd\Bundle\SettingsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Lotgd\Bundle\CoreBundle\Entity\Common\Deletable;
use Lotgd\Bundle\CoreBundle\Entity\Common\IdTrait;

/**
 * Domain for settings of LoTGD and for Bundles.
 *
 * @ORM\Entity(repositoryClass="Lotgd\Bundle\SettingsBundle\Repository\SettingDomainRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class SettingDomain
{
    use IdTrait;
    use Deletable;

    public const DEFAULT_NAME = 'default';

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $readOnly = false;

    /**
     * @ORM\OneToMany(targetEntity=Setting::class, mappedBy="domain")
     */
    private $settings;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->settings = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getReadOnly(): bool
    {
        return $this->readOnly;
    }

    public function setReadOnly(bool $readOnly): self
    {
        $this->readOnly = $readOnly;

        return $this;
    }

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
            $setting->setDomain($this);
        }

        return $this;
    }

    public function removeSetting(Setting $setting): self
    {
        if ($this->settings->removeElement($setting) && $setting->getDomain() === $this)
        {
            // set the owning side to null (unless already changed)
            $setting->setDomain(null);
        }

        return $this;
    }
}
