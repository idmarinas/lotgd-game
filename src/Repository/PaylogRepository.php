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
use Lotgd\Core\Entity\Paylog;

class PaylogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paylog::class);
    }

    /**
     * Update process of date.
     */
    public function updateProcessDate(): bool
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $result = $query->where("u.processdate = '0000-00-00' OR u.processdate = '0000-00-00 00:00:00'")
                ->getQuery()
                ->getResult()
            ;

            if ($result)
            {
                foreach ($result as $value)
                {
                    $value->setProcessdate($value->getInfo()['payment_date']);

                    $this->_em->persist($value);
                }

                $this->_em->flush();
            }

            return true;
        }
        catch (\Throwable $th)
        {
            return false;
        }
    }

    /**
     * Get a list of months.
     */
    public function getMonths(): array
    {
        try
        {
            return $this->_em->createQuery("SELECT month(u.processdate) AS month, (sum(u.amount)-sum(u.txfee)) AS profit, u.processdate AS date FROM {$this->_entityName} AS u GROUP BY month ORDER BY month DESC")
                ->getArrayResult()
            ;
        }
        catch (\Throwable $th)
        {
            return [];
        }
    }
}
