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

use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Lotgd\Core\Entity as LotgdEntity;
use Tracy\Debugger;

class MotdRepository extends DoctrineRepository
{
    /**
     * Get last MOTD.
     *
     * @param int|null $userId
     *
     * @return array|null
     */
    public function getLastMotd(?int $userId = null): ?array
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            $result = $qb->select('u', 'c.name as motdauthorname')
                ->leftJoin(LotgdEntity\Accounts::class, 'a', Join::WITH, $qb->expr()->eq('a.acctid', 'u.motdauthor'))
                ->leftJoin(LotgdEntity\Characters::class, 'c', Join::WITH, $qb->expr()->eq('c.id', 'a.character'))
                ->orderBy('u.motddate', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getArrayResult()
            ;

            if (! $result)
            {
                return null;
            }

            $motd = $result[0][0];
            unset($result[0][0]);
            $motd = array_merge($motd, $result[0]);

            //-- Is a poll
            if ($motd['motdtype'])
            {
                $motd = $this->appendPollResults($motd, $userId);
            }

            return $motd;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Get MoTD item info.
     *
     * @param int|null $userId
     *
     * @return array|null
     */
    public function getEditMotdItem(int $motdId): ?array
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            $result = $qb->select('u', 'c.name as motdauthorname')
                ->leftJoin(LotgdEntity\Accounts::class, 'a', Join::WITH, $qb->expr()->eq('a.acctid', 'u.motdauthor'))
                ->leftJoin(LotgdEntity\Characters::class, 'c', Join::WITH, $qb->expr()->eq('c.id', 'a.character'))
                ->where('u.motdtype = 0 AND u.motditem = :id')
                ->setParameter('id', $motdId)
                ->setMaxResults(1)
                ->getQuery()
                ->getArrayResult()
            ;

            if (! $result)
            {
                return null;
            }

            $motd = $result[0][0];
            unset($result[0][0]);

            return array_merge($motd, $result[0]);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Append results of a poll to MOTD item.
     *
     * @param array $motd   Information of a motd
     * @param int   $userId Id of user to get it's vote
     *
     * @return array
     */
    public function appendPollResults(array $motd, $userId): array
    {
        $q = $this->_em->createQuery('SELECT COUNT(p.resultid) AS votes, p.choice
            FROM Lotgd\Core\Entity\PollResults p
            WHERE p.motditem = :motditem
            GROUP BY p.choice
            ORDER BY p.choice
        ');
        $q->setParameter('motditem', $motd['motditem']);

        $result = $q->getArrayResult();

        //-- Add results to MOTD
        foreach ($result as $value)
        {
            $motd['pollResult']['opt'][$value['choice']] = (int) $value['votes'];
        }

        if ($userId)
        {
            $q = $this->_em->createQuery('SELECT p.choice FROM Lotgd\Core\Entity\PollResults p WHERE p.motditem = :motditem AND p.account = :acct');
            $q->setParameters([
                'motditem' => $motd['motditem'],
                'acct' => $userId
            ]);

            $result = $q->getOneOrNullResult();
            $motd['pollResult']['userVote'] = $result['choice'] ?? null;
        }

        if ($motd['pollResult']['opt'] ?? false)
        {
            $motd['pollResult']['totalVotes'] = \array_sum($motd['pollResult']['opt']);
        }

        //-- Unserialize information
        $motd['motdbody'] = \unserialize($motd['motdbody']);

        return $motd;
    }

    /**
     * Get a list of years with count of MoTD per month.
     *
     * @return array
     */
    public function getMonthCountPerYear()
    {
        $q = $this->_em->createQuery('SELECT YEAR(u.motddate) AS year, MONTH(u.motddate) AS month, u.motddate AS date, COUNT(MONTH(u.motddate)) AS count
            FROM Lotgd\Core\Entity\Motd u
            GROUP BY year, month
            ORDER BY year ASC, month ASC
        ');

        return $q->getArrayResult();
    }

    /**
     * Get last Motd date.
     *
     * @return \DateTime|null
     */
    public function getLastMotdDate(): ?\DateTime
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $date =  $query
                ->select('u.motddate')
                ->orderBy('u.motddate', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult()
            ;

            return new \DateTime($date);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }
}
