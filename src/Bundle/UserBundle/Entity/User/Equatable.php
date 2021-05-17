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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

trait Equatable
{
    /**
     * @ORM\Column(type="string", length=45)
     *
     * https://symfony.com/doc/4.4/security/user_provider.html#understanding-how-users-are-refreshed-from-the-session
     *
     * agregar un campo nuevo que indique q se está con el panel de administración o no para comprar uno u otro session id
     * agregar un session id para el panel de administración
     *
     * se puede usar un campo addicional para hacer referenia
     *
     * no funcionará,
     */
    protected $sessionId = '';

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Lotgd\Bundle\UserBundle\Entity\User $user
     */
    public function isEqualTo(UserInterface $user)
    {
        return ! (
            $this->getPassword() !== $user->getPassword()
            || $this->getSalt() !== $user->getSalt()
            || $this->getUsername() !== $user->getUsername()
            //-- Only allow 1 session in a unique device
            || $this->getSessionId() !== $user->getSessionId()
        );
    }
}
