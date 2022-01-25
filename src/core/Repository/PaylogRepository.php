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

use Throwable;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Paylog;
use Tracy\Debugger;

class PaylogRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

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
        catch (Throwable $th)
        {
            Debugger::log($th);

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
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Get list paylog.
     */
    public function getList(int $month): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $month     = $month ?: date('n');
            $month     = date('Y').'-'.$month;
            $startDate = $month.'-01 00:00:00';
            $endDate   = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($startDate)));

            return $query->select('u.payid', 'u.info', 'u.response', 'u.txnid', 'u.amount', 'u.name', 'u.acctid', 'u.processed', 'u.filed', 'u.txfee', 'u.processdate')
                ->addSelect('c.name')
                ->addSelect('a.donation', 'a.donationspent')
                ->leftJoin('LotgdCore:Avatar', 'c', 'WITH', $query->expr()->eq('c.acct', 'u.acctid'))
                ->leftJoin('LotgdCore:User', 'a', 'WITH', $query->expr()->eq('a.acctid', 'u.acctid'))

                ->where('u.processdate >= :start AND u.processdate < :end ')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)

                ->orderBy('u.processdate', Criteria::DESC)

                ->getQuery()
                ->getResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
