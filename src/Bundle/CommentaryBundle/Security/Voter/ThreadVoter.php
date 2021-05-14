<?php

namespace Lotgd\Bundle\CommentaryBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class ThreadVoter extends Voter
{
    public const CREATE = 'CREATE';
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return \in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof \Lotgd\Bundle\CommentaryBundle\Entity\Thread;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if ( ! $user instanceof UserInterface)
        {
            return false;
        }

        //-- Si es usuario es un super admin, no tiene límites de acceso
        if ($this->security->isGranted('ROLE_SUPER_ADMIN'))
        {
            return true;
        }

        switch ($attribute)
        {
            case self::CREATE: //-- Create/Reply a comment
            case self::VIEW: //-- View comments
                $permit = $this->security->isGranted('ROLE_USER');
            break;
            case self::EDIT: // edit comment
            case self::DELETE: // delete comment
                $permit = $this->security->isGranted('ROLE_ADMIN');
            break;
            default: $permit = false; break;
        }

        return $permit;
    }
}
