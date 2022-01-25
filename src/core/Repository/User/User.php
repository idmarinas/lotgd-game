<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Repository\User;

use Throwable;
use Laminas\Paginator\Paginator;
use Tracy\Debugger;

/**
 * Functions for user account.
 */
trait User
{
    /**
     * Get prefs of account.
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
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Search users.
     */
    public function userSearchAccounts(string $search, string $order, int $page): ?Paginator
    {
        $query = $this->createQueryBuilder('u');

        $query->select('u.acctid', 'u.emailaddress', 'u.lastip', 'u.laston', 'u.loggedin')
            ->addSelect('c.name', 'c.level')
            ->addSelect('p.gentimecount')
            ->leftJoin('LotgdCore:Avatar', 'c', 'WITH', $query->expr()->eq('c.id', 'u.avatar'))
            ->leftJoin('LotgdCore:AccountsEverypage', 'p', 'WITH', $query->expr()->eq('p.acctid', 'u.acctid'))
        ;

        //-- Order
        $sort = "u.{$order}";

        if ('name' == $order)
        {
            $sort = 'c.name';
        }
        $query->orderBy($sort, 'ASC');

        if ($search !== '' && $search !== '0')
        {
            $query->where('u.login LIKE :search OR c.name LIKE :search OR u.acctid = :search OR u.emailaddress LIKE :search OR u.lastip LIKE :search OR u.uniqueid LIKE :search')
                ->setParameter('search', "%{$search}%")
            ;
        }

        return $this->getPaginator($query, $page, 30);
    }
}
