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

namespace Lotgd\Bundle\AdminBundle\Security;

use Lotgd\Bundle\UserBundle\Security\UserChecker as SecurityUserChecker;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker extends SecurityUserChecker
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function checkPreAuth(UserInterface $user)
    {
        parent::checkPreAuth($user);

        if ( ! $this->security->isGranted('ROLE_ADMIN'))
        {
            $th = new CustomUserMessageAccountStatusException('user.role.insufficient');
            $th->setUser($user);

            throw $th;
        }
    }
}
