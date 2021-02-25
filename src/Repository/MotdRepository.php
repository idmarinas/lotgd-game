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
use Lotgd\Core\Entity\Motd;

class MotdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motd::class);
    }

    /**
     * Get a list of years with count of MoTD per month.
     *
     * @return array
     */
    public function getMonthCountPerYear()
    {
        $q = $this->_em->createQuery('SELECT YEAR(u.motddate) AS year, MONTH(u.motddate) AS month, u.motddate AS date, COUNT(MONTH(u.motddate)) AS count
            FROM LotgdCore:Motd u
            GROUP BY year, month
            ORDER BY year ASC, month ASC
        ');

        return $q->getArrayResult();
    }

    /**
     * Get last Motd date.
     */
    public function getLastMotdDate(): ?\DateTime
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $date = $query
                ->select('u.motddate')
                ->orderBy('u.motddate', \Doctrine\Common\Collections\Criteria::DESC)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult()
            ;

            return new \DateTime($date);
        }
        catch (\Throwable $th)
        {
            return null;
        }
    }
}
