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

class NewsRepository extends DoctrineRepository
{
    use News\Backup;

    /**
     * Delete a news by ID.
     *
     * @param int $newsId
     *
     * @return bool
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
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return false;
        }
    }

    /**
     * Delte old news in data base.
     *
     * @param int $expire
     *
     * @return int
     */
    public function deleteExpireNews(int $expire): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval("P{$expire}D"));

            return $query->delete($this->_entityName, 'u')
                ->where('u.newsdate < :date')
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
