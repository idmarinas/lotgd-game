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

use Lotgd\Core\Repository\News\Backup;
use Throwable;
use Tracy\Debugger;
use DateTime;
use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\News as NewsEntity;

class NewsRepository extends ServiceEntityRepository implements RepositoryBackupInterface
{
    use Backup;
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsEntity::class);
    }

    /**
     * Delete a news by ID.
     */
    public function deleteNewsId(int $newsId): bool
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.newsid = :id')
                ->setParameters(['id' => $newsId])
                ->getQuery()
                ->execute()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Delte old news in data base.
     */
    public function deleteExpireNews(int $expire): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new DateTime('now');
            $date->sub(new DateInterval("P{$expire}D"));

            return $query->delete($this->_entityName, 'u')
                ->where('u.date < :date')
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
