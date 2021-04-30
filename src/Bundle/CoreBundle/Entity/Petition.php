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

namespace Lotgd\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Lotgd\Bundle\CoreBundle\Repository\PetitionRepository;
use Lotgd\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PetitionRepository::class)
 */
class Petition
{
    use Common\IdTrait;
    use TimestampableEntity;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Avatar::class)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     * @Assert\Length(
     *     min=0,
     *     max=120
     * )
     */
    private $avatarName;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=3,
     *     max=255
     * )
     */
    private $userOfAvatar;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=5,
     *     max=255
     * )
     */
    private $subject = '';

    /**
     * @ORM\Column(type="text", length=65535, options={"collation": "utf8mb4_unicode_ci"})
     *
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=20,
     *     max=65535
     * )
     */
    private $description = '';

    /**
     * @ORM\ManyToOne(targetEntity=PetitionType::class)
     */
    private $problemType;

    /**
     * @ORM\Column(type="petition_status_type_enum", options={"default": "unhandled"})
     */
    private $status = 'unhandled';

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $ipAddress;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closeDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $closeUser;

    public function __toString(): string
    {
        return $this->getUserOfAvatar().') '.$this->getAvatarName() ?: '-';
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

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }


    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProblemType(): ?PetitionType
    {
        return $this->problemType;
    }

    public function setProblemType(?PetitionType $problemType): self
    {
        $this->problemType = $problemType;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(?string $avatarName): self
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    public function getUserOfAvatar(): ?string
    {
        return $this->userOfAvatar;
    }

    public function setUserOfAvatar(?string $userOfAvatar): self
    {
        $this->userOfAvatar = $userOfAvatar;

        return $this;
    }

    public function getCloseDate(): ?\DateTimeInterface
    {
        return $this->closeDate;
    }

    public function setCloseDate(?\DateTimeInterface $closeDate): self
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    public function getCloseUser(): ?User
    {
        return $this->closeUser;
    }

    public function setCloseUser(?User $closeUser): self
    {
        $this->closeUser = $closeUser;

        return $this;
    }
}
