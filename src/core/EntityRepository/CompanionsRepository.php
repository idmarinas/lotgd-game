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

class CompanionsRepository extends DoctrineRepository
{
    /**
     * Get a list of available mecenaries.
     *
     * @param string $location
     * @param int    $dragonKills
     *
     * @return array
     */
    public function getMercenaryList(string $location, int $dragonKills): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query
                ->where('u.companioncostdks <= :dk')
                ->andWhere("u.companionlocation = :loc OR u.companionlocation = 'all'")
                ->andWhere('u.companionactive = 1')

                ->setParameter('dk', $dragonKills)
                ->setParameter('loc', $location)

                ->getQuery()
                ->getResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
