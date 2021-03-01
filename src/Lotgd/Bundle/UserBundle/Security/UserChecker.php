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

namespace Lotgd\Bundle\UserBundle\Security;

use Lotgd\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatableMessage;

class UserChecker implements UserCheckerInterface
{
    protected $accessDecisionManager;
    protected $flash;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager, FlashBagInterface $flash)
    {
        $this->accessDecisionManager = $accessDecisionManager;
        $this->flash                 = $flash;
    }

    public function checkPreAuth(UserInterface $user)
    {
        if ( ! $user instanceof User)
        {
            return;
        }

        if ($user->isDeleted())
        {
            throw new CustomUserMessageAccountStatusException('user.account.no.exist');
        }

        if ($user->isBanned())
        {
            throw new CustomUserMessageAccountStatusException('user.account.banned');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if ( ! $user instanceof User)
        {
            return;
        }

        if ( ! $user->isVerified())
        {
            $this->flash->add('warning', new TranslatableMessage('user.email.not.verified', [], 'lotgd_user_bundle'));
        }

        // user account is expired, the user may be notified
        // if ($user->isExpired())
        // {
        //     throw new AccountExpiredException('...');
        // }
    }
}
