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
}
