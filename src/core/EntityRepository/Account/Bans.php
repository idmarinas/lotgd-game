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

namespace Lotgd\Core\EntityRepository\Account;

use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Entity as LotgdEntity;
use Tracy\Debugger;
use Zend\Paginator\Paginator;

/**
 * Functions for bans account.
 */
trait Bans
{
    /**
     * Search accounts.
     *
     * @param string $search
     * @param string $order
     * @param int    $page
     *
     * @return Paginator|null
     */
    public function bansSearchAccts(string $search, string $order, int $page): ?Paginator
    {
        $query = $this->createQueryBuilder('u');

        $query->select('u.acctid', 'u.login', 'u.emailaddress', 'u.lastip', 'u.uniqueid', 'u.laston', 'u.loggedin')
            ->addSelect('c.name', 'c.level')
            ->addSelect('p.gentimecount')
            ->leftJoin(LotgdEntity\Characters::class, 'c', Join::WITH, $query->expr()->eq('c.id', 'u.character'))
            ->leftJoin(LotgdEntity\AccountsEverypage::class, 'p', Join::WITH, $query->expr()->eq('p.acctid', 'u.acctid'))
        ;

        //-- Order
        $sort = "u.{$order}";

        if ('name' == $order)
        {
            $sort = 'c.name';
        }
        elseif ('level' == $order)
        {
            $sort = 'c.level';
        }
        $query->orderBy($sort, 'ASC');

        if ($search)
        {
            $query->where("u.login LIKE :search OR c.name LIKE :search OR u.acctid = :search OR u.emailaddress LIKE :search OR u.lastip LIKE :search OR u.uniqueid LIKE :search")
                ->setParameter('search', "%{$search}%")
            ;
        }

        return $this->getPaginator($query, $page, 30);
    }

    /**
     * Get a basic information of account for ban.
     *
     * @param int $acctId
     *
     * @return array
     */
    public function getBasicInfoOfAccount(int $acctId): array
    {
        try
        {
            $query = $this->createQueryBuilder('u');

            return $query->select('u.acctid', 'u.lastip', 'u.uniqueid')
                ->addSelect('c.name')
                ->leftJoin(LotgdEntity\Characters::class, 'c', Join::WITH, $query->expr()->eq('c.id', 'u.character'))
                ->where('u.acctid = :id')
                ->setParameter('id', $acctId)
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

    /**
     * Get accounts with identical ID.
     *
     * @param string $uniqueId
     *
     * @return array
     */
    public function getAccountsWithEqualId(string $uniqueId): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.acctid', 'u.lastip', 'u.uniqueid', 'u.laston')
                ->addSelect('c.name')
                ->addSelect('p.gentimecount')
                ->leftJoin(LotgdEntity\Characters::class, 'c', Join::WITH, $query->expr()->eq('c.id', 'u.character'))
                ->leftJoin(LotgdEntity\AccountsEverypage::class, 'p', Join::WITH, $query->expr()->eq('p.acctid', 'u.acctid'))
                ->where('u.uniqueid = :id')
                ->orderBy('u.lastip', 'DESC')
                ->setParameter('id', $uniqueId)
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
     * Get accounts with similar IP.
     *
     * @param string $ip
     * @param int    $accountId
     *
     * @return array
     */
    public function getAccountsWithSimilarIp(string $ip, int $accountId): array
    {
        $query = $this->createQueryBuilder('u');
        $expr = $query->expr();

        try
        {
            $query->select('u.acctid', 'u.lastip', 'u.uniqueid', 'u.laston')
                ->addSelect('c.name')
                ->addSelect('p.gentimecount')
                ->leftJoin(LotgdEntity\Characters::class, 'c', Join::WITH, $query->expr()->eq('c.id', 'u.character'))
                ->leftJoin(LotgdEntity\AccountsEverypage::class, 'p', Join::WITH, $query->expr()->eq('p.acctid', 'u.acctid'))
                ->orderBy('u.uniqueid', 'DESC')
            ;
            $dots = 0;

            for ($x = strlen($ip); $x > 0; $x--)
            {
                if ($dots > 1)
                {
                    break;
                }
                $thisip = substr($ip, 0, $x);
                $query->orWhere("u.lastip LIKE ?$x")
                    ->setParameter($x, "{$thisip}%")
                ;

                if ('.' == substr($ip, $x - 1, 1))
                {
                    $x--;
                    $dots++;
                }
            }

            //-- Avoid including the account being compared
            $query->andWhere($expr->not('u.acctid = :acct'))
                ->setParameter('acct', $accountId)
            ;

            $cosa = $query->getQuery();

            bdump($cosa);
            bdump($cosa->getSql());

            return $cosa
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
     * Log out affected players for a ban.
     *
     * @param string $ip
     * @param string $id
     * @param string $type
     *
     * @return int
     */
    public function logoutAffectedAccounts(string $ip, string $id, string $type): int
    {
        try
        {
            $query = $this->_em->createQueryBuilder()->update($this->_entityName, 'u');

            $query->set('u.loggedin', 0);

            if ('ip' == $type)
            {
                $query->where('u.lastip = :ip')
                    ->setParameter('ip', $ip)
                ;
            }
            else
            {
                $query->where('u.uniqueid = :id')
                    ->setParameter('id', $id)
                ;
            }

            return $query->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
