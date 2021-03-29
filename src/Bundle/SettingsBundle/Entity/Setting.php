<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.md
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Lotgd\Bundle\CoreBundle\Entity\Common\Deletable;
use Lotgd\Bundle\CoreBundle\Entity\Common\IdTrait;
use Lotgd\Bundle\SettingsBundle\Repository\SettingRepository;
use Lotgd\Bundle\UserBundle\Entity\User;

/**
 * Settings of LoTGD and for Bundles.
 *
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="lotgd_settings_bundle_setting", columns={"domain_id", "name", "user_id"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass=SettingRepository::class)
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Setting
{
    use IdTrait;
    use Deletable;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=SettingDomain::class, inversedBy="settings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $description;

    /**
     * @ORM\Column(type="setting_type_enum")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $value = '';

    /**
     * It is only necessary if the setting is associated with the user.
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="settings")
     */
    private $user;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->domain . ') ' . $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDomain(): ?SettingDomain
    {
        return $this->domain;
    }

    public function setDomain(?SettingDomain $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function formatedValue()
    {
        switch ($this->getType())
        {
            case 'bool':
                $formated = (bool) $this->getValue();
            break;
            case 'int':
                $formated = (int) $this->getValue();
            break;
            case 'float':
                $formated = (float) $this->getValue();
            break;
            default: //-- Default is string
                $formated = (string) $this->getValue();
            break;
        }

        return $formated;
    }
}
