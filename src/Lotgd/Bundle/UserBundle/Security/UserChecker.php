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
use Lotgd\Bundle\UserBundle\Exception\AccountBannedException;
use Lotgd\Bundle\UserBundle\Exception\AccountDeletedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{
    protected $accessDecisionManager;
    protected $flash;
    protected $translator;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager, FlashBagInterface $flash, TranslatorInterface $translator)
    {
        $this->accessDecisionManager = $accessDecisionManager;
        $this->flash                 = $flash;
        $this->translator            = $translator;
    }

    public function checkPreAuth(UserInterface $user)
    {
        if ( ! $user instanceof User)
        {
            return;
        }

        if ($user->isDeleted())
        {
            $exp = new AccountDeletedException();
            $exp->setUser($user);

            throw $exp;
        }

        if ($user->isBanned())
        {
            $exp = new AccountBannedException();
            $exp->setUser($user);

            throw $exp;
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
            $this->flash->add('warning', $this->translator->trans('user.email.not.verified', [], 'lotgd_user_bundle'));
        }

        // user account is expired, the user may be notified
        // if ($user->isExpired())
        // {
        //     throw new AccountExpiredException('...');
        // }
    }
}
