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

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Entity as LotgdEntity;

class PetitionsRepository extends EntityRepository
{
    /**
     * Get a list count of petitions.
     *
     * @return array
     */
    public function getStatusListCount(): array
    {
        $qb = $this->createQueryBuilder('u');
        $petitions = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];

        try
        {
            $result = $qb->select('u.status', 'COUNT(1) AS c')
                ->groupBy('u.status')
                ->getQuery()
                ->getResult()
            ;

            foreach($result as $row)
            {
                $petitions[(int) $row['status']] = (int) $row['c'];
            }

            return $petitions;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return $petitions;
        }
    }
}
