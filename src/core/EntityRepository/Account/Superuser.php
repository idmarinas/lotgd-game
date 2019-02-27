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
 * Functions for superuser account.
 */
trait Superuser
{
    /**
     * Get all superusers who have the given permission.
     *
     * @param int $permit
     *
     * @return array
     */
    public function getSuperuserWithPermit(int $permit): ?array
    {
        try
        {
            $qb = $this->createQueryBuilder('u');

            return $qb->select('u.acctid')
                ->where('BIT_AND(u.superuser, :permit) > 0')
                ->setParameter('permit', $permit)
                ->getQuery()
                ->getArrayResult()
            ;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return null;
        }
    }
}
