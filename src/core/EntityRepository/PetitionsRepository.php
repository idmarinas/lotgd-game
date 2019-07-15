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

class PetitionsRepository extends DoctrineRepository
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

            foreach ($result as $row)
            {
                $petitions[(int) $row['status']] = (int) $row['c'];
            }

            return $petitions;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return $petitions;
        }
    }

    /**
     * Get count of petitions for network.
     *
     * @param string $ip
     * @param string $lgi
     *
     * @return int
     */
    public function getCountPetitionsForNetwork(string $ip, string $lgi): int
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval('P1D'));

            return $query->select('count(u.petitionid)')
                ->where('inet_aton(u.ip) LIKE inet_aton(:ip) OR u.id = :lgi')
                ->andWhere('u.date > :date')
                ->setParameter('date', $date)
                ->setParameter('ip', $ip)
                ->setParameter('lgi', $lgi)

                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }

    /**
     * Delete old petitions.
     *
     * @return bool
     */
    public function deleteOldPetitions(): bool
    {
        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval('P7D'));

            $query = $this->_em->createQueryBuilder();

            return $query->delete($this->_entityName, 'u')
                ->where('u.status = :status AND u.closedate <= :date')
                ->setParameter('date', $date)
                ->setParameter('status', 2)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
