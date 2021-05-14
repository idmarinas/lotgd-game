<?php

namespace Lotgd\Bundle\CommentaryBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Lotgd\Bundle\CommentaryBundle\Entity\Comment;
use Lotgd\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Security;
use FOS\CommentBundle\Model\CommentInterface;

class CommentVoter extends Voter
{
    public const CREATE = 'CREATE';
    public const REPLY = 'REPLY';
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
        return \in_array($attribute, [self::CREATE, self::REPLY, self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    /**
     * @param Comment $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if ( ! $user instanceof UserInterface)
        {
            return false;
        }

        //-- Super admin not have limits
        if ($this->security->isGranted('ROLE_SUPER_ADMIN'))
        {
            return true;
        }

        switch ($attribute)
        {
            case self::CREATE: //-- Create/Reply a new comment
            case self::VIEW: //-- View comments
                $permit = $this->security->isGranted('ROLE_USER');
            break;
            case self::REPLY:
                $permit = $this->canReply($subject);
            break;
            case self::EDIT: // edit comment
            case self::DELETE: // delete comment
                $permit = $this->canEditDelete($subject, $user);
            break;
            default: $permit = false; break;
        }

        return $permit;
    }

    /**
     * User can delete/edit own comments if no more than 5 minutes have elapsed.
     * Or if have ROLE_ADMIN
     */
    private function canEditDelete(Comment $subject, User $user): bool
    {
        $elapsed = (new \DateTime('now'))->sub(new \DateInterval('PT5M'));

        return (
            $this->security->isGranted('ROLE_ADMIN')
            || (
                ! $subject->isSystemComment()
                && ! $subject->isGameComment()
                && $subject->getAuthor()->getId() == $user->getId()
                && $subject->getCreatedAt() > $elapsed
                && CommentInterface::STATE_VISIBLE == $subject->getState()
                )
        );
    }

    /**
     * User can reply to comments only if no more than 1 month have elapsed.
     * Or if have ROLE_ADMIN
     */
    private function canReply(Comment $subject): bool
    {
        $elapsed = (new \DateTime('now'))->sub(new \DateInterval('P1M'));

        return (
            ($this->security->isGranted('ROLE_ADMIN') && ! $subject->isSystemComment() && ! $subject->isGameComment())
            || (
                ! $subject->isSystemComment()
                && ! $subject->isGameComment()
                && CommentInterface::STATE_VISIBLE == $subject->getState()
                && $subject->getCreatedAt() > $elapsed
                )
        );
    }
}
