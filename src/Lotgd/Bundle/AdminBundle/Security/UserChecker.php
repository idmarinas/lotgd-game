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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

class UserChecker extends SecurityUserChecker
{
    public function checkPostAuth(UserInterface $user)
    {
        parent::checkPostAuth($user);

        $token = new PreAuthenticatedToken($user, [], 'admin', $user->getRoles());

        if ( ! $this->accessDecisionManager->decide($token, ['ROLE_ADMIN'], null))
        {
            $th = new CustomUserMessageAccountStatusException('user.role.insufficient');
            $th->setUser($user);

            throw $th;
        }
    }
}
