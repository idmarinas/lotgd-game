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

namespace Lotgd\Core\Entity\User;

trait Security
{
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password = '';

    /**
     * @var string
     *
     * @ORM\Column(name="forgottenpassword", type="string", nullable=true)
     */
    private $forgottenpassword = '';

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the value of Forgottenpassword.
     *
     * @param string $forgottenpassword
     */
    public function setForgottenpassword($forgottenpassword): self
    {
        $this->forgottenpassword = $forgottenpassword;

        return $this;
    }

    /**
     * Get the value of Forgottenpassword.
     */
    public function getForgottenpassword(): string
    {
        return $this->forgottenpassword;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function eraseDataForCache()
    {
        $this->password = null;
    }
}
