<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Repository;

use Lotgd\Core\Repository\Commentary\Backup;
use Throwable;
use Doctrine\Common\Collections\Criteria;
use DateTime;
use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Commentary as EntityCommentary;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tracy\Debugger;

class CommentaryRepository extends ServiceEntityRepository implements RepositoryBackupInterface
{
    use Backup;
    use EntityRepositoryTrait;

    private $translator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry, EntityCommentary::class);

        $this->translator = $translator;
    }

    /**
     * Save comment to data base.
     *
     * @param Commentary $commentary
     */
    public function saveComment(EntityCommentary $commentary): bool
    {
        try
        {
            $this->_em->persist($commentary);
            $this->_em->flush();

            return true;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Hide/Unhide comments.
     */
    public function moderateComments(array $post): bool
    {
        global $session;

        try
        {
            $keys = array_keys($post);

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
                $message = $this->translator->trans('comment.moderation.hide', ['name' => $session['user']['name']], 'app_commentary');
                //--  Unhide message
                if ($hiddenOld && ! $hiddenNew)
                {
                    $message = $this->translator->trans('comment.moderation.unhide', ['name' => $session['user']['name']], 'app_commentary');
                }

                $comment->setHiddenComment($message);
            }

            $this->_em->flush();

            return true;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Get all sections in comentary.
     */
    public function getPublishedSections(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $result = $query->select('u.section')
                ->where('u.section NOT LIKE :section')
                ->groupBy('u.section')
                ->orderBy('u.section', Criteria::ASC)

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
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Delte old comments in data base.
     */
    public function deleteExpireComments(int $expire): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new DateTime('now');
            $date->sub(new DateInterval("P{$expire}D"));

            return $query->delete($this->_entityName, 'u')
                ->where('u.postdate < :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->execute()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
