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
            \Tracy\Debugger::log($th);

            return [];
        }
    }
}
