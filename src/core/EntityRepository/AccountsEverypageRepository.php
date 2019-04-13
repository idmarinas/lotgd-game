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
use Tracy\Debugger;

class AccountsEverypageRepository extends DoctrineRepository
{
    /**
     * Get stats of page gen.
     *
     * @return array
     */
    public function getStatsPageGen(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('SUM(u.gentime) AS gentime', 'SUM(u.gentimecount) AS gentimecount', 'SUM(u.gensize) AS gensize')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
