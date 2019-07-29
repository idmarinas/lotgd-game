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

class MountsRepository extends DoctrineRepository
{
    /**
     * Get list of mounts with owners.
     *
     * @return array
     */
    public function getList(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.mountid', 'u.mountname', 'u.mountactive', 'u.mountcategory', 'u.mountforestfights', 'u.mountdkcost', 'u.mountcostgems', 'u.mountcostgold')
                ->addSelect('count(c.hashorse) AS owners')

                ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $query->expr()->eq('c.hashorse', 'u.mountid'))

                ->groupBy('u.mountid')

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

    /**
     * Refund cost of mount to players.
     *
     * @param object $entity
     *
     * @return bool
     */
    public function refundMount($entity): bool
    {
        try
        {
            $query = $this->_em->createQuery("UPDATE LotgdCore:Characters u SET u.gems = u.gems+?2, u.goldinbank = u.goldinbank+?3, u.hashorse = '0' WHERE u.hashorse = ?1");

            $query->setParameter(1, $entity->getMountid())
                ->setParameter(2, $entity->getMountcostgems())
                ->setParameter(3, $entity->getMountcostgold())
                ->execute()
            ;

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Get mounts by location (include all).
     *
     * @param string $location
     *
     * @return array
     */
    public function getMountsByLocation(string $location): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query
                ->where('u.mountactive = 1')
                ->andWhere('u.mountlocation = :all OR u.mountlocation = :loc')

                ->orderBy('u.mountcategory', 'ASC')
                ->addOrderBy('u.mountcostgems', 'ASC')
                ->addOrderBy('u.mountcostgold', 'ASC')

                ->setParameter('all', 'all')
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
