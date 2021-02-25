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

namespace Lotgd\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Entity\Commentary;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommentaryRepository extends ServiceEntityRepository
{
    protected $translator;
    protected $security;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator, Security $security)
    {
        parent::__construct($registry, Commentary::class);

        $this->translator = $translator;
        $this->security   = $security;
    }

    /**
     * Hide/Unhide comments.
     */
    public function moderateComments(array $post): bool
    {
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

                /** @var Lotgd\Core\Entity\User */
                $user = $this->security->getUser();
                $comment->setHidden($hiddenNew);
                $comment->setHiddenBy($user->getId());
                $comment->setHiddenByName($user->getUsername());

                //-- Hide message
                $message = $this->translator->trans('comment.moderation.hide', ['name' => $user->getUsername()], 'app_commentary');
                //--  Unhide message
                if ($hiddenOld && ! $hiddenNew)
                {
                    $message = $this->translator->trans('comment.moderation.unhide', ['name' => $user->getUsername()], 'app_commentary');
                }

                $comment->setHiddenComment($message);
            }

            $this->_em->flush();

            return true;
        }
        catch (\Throwable $th)
        {
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
                ->orderBy('u.section', \Doctrine\Common\Collections\Criteria::ASC)

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
            return 0;
        }
    }
}
