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

use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;

class FaillogRepository extends DoctrineRepository
{
    /**
     * Delte old fail logs in data base.
     */
    public function deleteExpireFaillogs(int $expire): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval("P{$expire}D"));

            return $query->delete($this->_entityName, 'u')
                ->where('u.date < :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return 0;
        }
    }
}
