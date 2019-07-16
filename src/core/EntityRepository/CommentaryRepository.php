<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Lotgd\Core\Entity\Commentary;
use Tracy\Debugger;

class CommentaryRepository extends DoctrineRepository
{
    /**
     * Save comment to data base.
     *
     * @param Commentary $commentary
     *
     * @return bool
     */
    public function saveComment(Commentary $commentary): bool
    {
        try
        {
            $this->_em->persist($commentary);
            $this->_em->flush();

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Hide/Unhide comments.
     *
     * @return bool
     */
    public function moderateComments(array $post): bool
    {
        global $session;

        try
        {
            $keys = \array_keys($post);

            $result = $this->findBy(['id' => $keys]);

            foreach ($result as $comment)
            {
                $commentId = $comment->getId();
                $hiddenOld = $comment->getHidden();
                $hiddenNew = (bool) $post[$commentId];

                //-- Ignore if no changes
                if ($hiddenOld == $hiddenNew)
                {
                    continue;
                }

                $comment->setHidden($hiddenNew);
                $comment->setHiddenBy($session['user']['acctid']);
                $comment->setHiddenByName($session['user']['name']);

                //-- Hide message
                $message = \LotgdTranslator::t('comment.moderation.hide', ['name' => $session['user']['name']], 'app-commentary');
                //--  Unhide message
                if ($hiddenOld && ! $hiddenNew)
                {
                    $message = \LotgdTranslator::t('comment.moderation.unhide', ['name' => $session['user']['name']], 'app-commentary');
                }

                $comment->setHiddenComment($message);
            }

            $this->_em->flush();

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Get all sections in comentary.
     *
     * @return array
     */
    public function getPublishedSections(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $result = $query->select('u.section')
                ->where('u.section NOT LIKE :section')
                ->groupBy('u.section')
                ->orderBy('u.section', 'ASC')

                ->setParameter('section', 'clan-%')

                ->getQuery()
                ->getResult()
            ;

            $sections = [];

            foreach ($result as $section)
            {
                $sections[] = $section['section'];
            }

            return $sections;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Delete comments of account.
     *
     * @param int $accountId
     *
     * @return int
     */
    public function deleteCommentsOfAccount(int $accountId): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.author = :acct')
                ->setParameter('acct', $accountId)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }

    /**
     * Delte old comments in data base.
     *
     * @param int $expire
     *
     * @return int
     */
    public function deleteExpireComments(int $expire): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval("P{$expire}D"));

            return $query->delete($this->_entityName, 'u')
                ->where('u.postdate < :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
