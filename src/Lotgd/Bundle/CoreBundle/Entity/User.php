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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Lotgd\Bundle\CoreBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Structure of table "user" in data base.
 *
 * This table store users, only data related to user.
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="entity.user.username.not.unique")
 */
class User implements UserInterface
{
    use Common\IdTrait;
    use User\Avatar;
    use User\Ban;
    use User\Donation;
    use User\Security;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastMotd;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $referer;

    /**
     * @ORM\Column(type="boolean")
     */
    private $refererIsRewarded = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function __construct()
    {
        $this->avatars   = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $merged = $this->roles;
        // guarantee every user at least has ROLE_USER
        $merged[] = 'ROLE_USER';

        return \array_unique($merged);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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

    public function getLastMotd(): ?\DateTimeInterface
    {
        return $this->lastMotd;
    }

    public function setLastMotd(?\DateTimeInterface $lastMotd): self
    {
        $this->lastMotd = $lastMotd;

        return $this;
    }

    public function getReferer(): ?self
    {
        return $this->referer;
    }

    public function setReferer(?self $referer): self
    {
        $this->referer = $referer;

        return $this;
    }

    public function getRefererIsRewarded(): ?bool
    {
        return $this->refererIsRewarded;
    }

    public function setRefererIsRewarded(bool $refererIsRewarded): self
    {
        $this->refererIsRewarded = $refererIsRewarded;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}