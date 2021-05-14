<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CommentaryBundle\EventSubscriber;

use FOS\CommentBundle\Event\CommentPersistEvent;
use FOS\CommentBundle\Events;
use Laminas\Filter;
use Lotgd\Bundle\CommentaryBundle\Entity\Comment;
use Lotgd\Bundle\CoreBundle\Tool\Censor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class CommentarySubscriber implements EventSubscriberInterface
{
    private $security;
    private $censor;

    public function __construct(Security $security, Censor $censor)
    {
        $this->security = $security;
        $this->censor   = $censor;
    }

    public function onCommentPrePersist(CommentPersistEvent $event): void
    {
        /** @var Comment */
        $object = $event->getComment();
        $this->basicProcess($object);

        //-- Process for system or user
        if ($object->isSystemComment())
        {
            $this->proccessSystemComment($object);

            return;
        }

        $this->proccessUserComment($object);
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::COMMENT_PRE_PERSIST => 'onCommentPrePersist',
        ];
    }

    /**
     * Basic proccess of comment.
     */
    private function basicProcess(Comment $object): void
    {
        //-- Sanitize comment
        $body = $this->cleanComment($object->getBody());

        $object->setBody($body);
        $object->setRawBody($body);

        //-- Process commands in comment
        $this->processCommands($object);
    }

    /**
     * Proccess comment from user.
     * Can be a /game command but need proccess too.
     * /game is a special command
     *  -   From user is a Game Master message.
     *  -   From system is a message of ROLE play of game. (Automatic game messages)
     */
    private function proccessUserComment(Comment $object): void
    {
        //-- Command can be from user or for system
        if ('game' == $object->getCommand() && ! $object->getAuthor())
        {
            //-- If message is from system not apply profanity filter.
            return;
        }

        $avatar = $object->getAuthor()->getAvatar();
        $clan   = $avatar ? $avatar->getClan() : null;

        //-- Add data
        $object
            ->setAvatar($avatar)
            ->setAuthorName($avatar ? $avatar->getName() : '')
            ->setClan($clan ?: null)
            ->setClanRank($avatar ? $avatar->getClanrank() : 0)
            ->setClanName($clan ? $clan->getClanname() : '')
            ->setClanNameShort($clan ? $clan->getClanshort() : '')
        ;

        //-- Apply profanity filter
        $comment = $object->getBody();
        $object->setBody($this->censor->filter($comment));
        $object->setUncesoredBody($comment);
        $object->setCensoredWords($this->censor->getMatchWords());

        //-- Set last comment time
        $avatar && $avatar->setRecentcomments(new \DateTime('now'));
    }

    /**
     * Proccess comment for system.
     */
    private function proccessSystemComment(Comment $object): void
    {
        //-- Only system can use this command (no author)
        if ('/system' == \substr($object->getBody(), 0, 7))
        {
            $object->setBody(\trim(\substr($object->getBody(), 7)));
        }

        $object->setCommand('system');
    }

    /**
     * Clean comment for safety insert in DB.
     */
    private function cleanComment(?string $comment): string
    {
        $filterChain = new Filter\FilterChain();
        $filterChain
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags())
            // ->attach(new Filter\StripNewlines())
            // ->attach(new Filter\PregReplace(['pattern' => '/`n/', 'replacement' => '']))
            ->attach(new Filter\PregReplace(['pattern' => '/([^[:space:]]{45,45})([^[:space:]])/', 'replacement' => '\\1 \\2']))
            // ->attach(new Filter\Callback([new \HTMLPurifier(), 'purify']), -1) //-- Executed last in query
        ;

        //-- Only accept correct format, all italic open tag need close tag.
        //-- Other wise, italic format is removed.
        if (\substr_count($comment, '`i') != \substr_count($comment, '´i'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`i/', '/´i/'], 'replacement' => '']));
        }

        //-- Only accept correct format, all bold open tag need close tag.
        //-- Other wise, bold format is removed.
        if (\substr_count($comment, '`b') != \substr_count($comment, '´b'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`b/', '/´b/'], 'replacement' => '']));
        }

        //-- Only accept correct format, all center open tag need close tag.
        //-- Other wise, center format is removed.
        if (\substr_count($comment, '`c') != \substr_count($comment, '´c'))
        {
            $filterChain->attach(new Filter\PregReplace(['pattern' => ['/`c/', '/´c/'], 'replacement' => '']));
        }

        return $filterChain->filter($comment);
    }

    /**
     * Process commands for comment.
     */
    private function processCommands(Comment $object): bool
    {
        $comment = $object->getBody();

        if ('/me' == \substr($comment, 0, 3))
        {
            $comment = \trim(\substr($comment, 3));
            $command = 'me';
        }
        elseif ('::' == \substr($comment, 0, 1))
        {
            $comment = \trim(\substr($comment, 2));
            $command = 'me';
        }
        elseif (':' == \substr($comment, 0, 1))
        {
            $comment = \trim(\substr($comment, 1));
            $command = 'me';
        }
        //-- Only admins can use this command
        elseif ('/game' == \substr($comment, 0, 5) && ($object->isSystemComment() || $this->security->isGranted('ROLE_ADMIN')))
        {
            $comment = \trim(\substr($comment, 5));
            $command = 'game';
        }

        $object->setBody($comment);
        $object->setCommand($command ?? '');

        //-- If process special commands return
        return (bool) ($command ?? false);
    }
}
