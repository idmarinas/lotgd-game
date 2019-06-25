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

class AccountsOutputRepository extends DoctrineRepository
{
    /**
     * Get output code for account.
     *
     * @param int $acctId
     *
     * @return string
     */
    public function getOutput(int $acctId): string
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.output')
                ->where('u.acctid = :acct')
                ->setParameter('acct', $acctId)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return '';
        }
    }

    /**
     * Delete output of account.
     *
     * @param int $accountId
     *
     * @return int
     */
    public function deleteOutputOfAccount(int $accountId): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.acctid = :acct')
                ->setParameter('acct', $accountId)
                ->getQuery()
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
