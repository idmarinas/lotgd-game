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

namespace Lotgd\Core\Repository\News;

use Throwable;
use Tracy\Debugger;

/**
 * Functions for backup data.
 */
trait Backup
{
    /**
     * Get all news of account.
     */
    public function backupGetDataFromAccount(int $accountId): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->where('u.accountId = :acct')

                ->setParameter('acct', $accountId)

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

    /**
     * Delete news of account.
     */
    public function backupDeleteDataFromAccount(int $accountId): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.accountId = :acct')
                ->setParameter('acct', $accountId)
                ->getQuery()
                ->execute()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
