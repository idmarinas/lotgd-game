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

use Tracy\Debugger;
use Zend\Paginator\Paginator;

/**
 * Functions for user account.
 */
trait User
{
    /**
     * Get prefs of account.
     *
     * @param int $acctId
     *
     * @return array
     */
    public function getAcctPrefs(int $acctId)
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            $result = $qb->select('u.prefs')
                ->where('u.acctid = :acct')
                ->setParameter('acct', $acctId)
                ->setMaxResults(1)
                ->getQuery()
                ->getArrayResult()
            ;

            return $result[0]['prefs'];
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Search users.
     *
     * @param string $search
     * @param string $order
     * @param int    $page
     *
     * @return Paginator|null
     */
    public function userSearchAccounts(string $search, string $order, int $page): ?Paginator
    {
        $query = $this->createQueryBuilder('u');

        $query->select('u.acctid', 'u.emailaddress', 'u.lastip', 'u.laston', 'u.loggedin')
            ->addSelect('c.name', 'c.level')
            ->addSelect('p.gentimecount')
            ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $query->expr()->eq('c.id', 'u.character'))
            ->leftJoin('LotgdCore:AccountsEverypage', 'p', 'WITH', $query->expr()->eq('p.acctid', 'u.acctid'))
        ;

        //-- Order
        $sort = "u.{$order}";

        if ('name' == $order)
        {
            $sort = 'c.name';
        }
        $query->orderBy($sort, 'ASC');

        if ($search)
        {
            $query->where('u.login LIKE :search OR c.name LIKE :search OR u.acctid = :search OR u.emailaddress LIKE :search OR u.lastip LIKE :search OR u.uniqueid LIKE :search')
                ->setParameter('search', "%{$search}%")
            ;
        }

        return $this->getPaginator($query, $page, 30);
    }
}
