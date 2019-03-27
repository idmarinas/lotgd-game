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

    /**
     * Get all superusers who have the given permission.
     *
     * @param int $permit
     *
     * @return int
     */
    public function getSuperuserCountWithPermit(int $permit): int
    {
        try
        {
            $qb = $this->createQueryBuilder('u');

            return $qb->select('COUNT(1)')
                ->where('BIT_AND(u.superuser, :permit) > 0')
                ->setParameter('permit', $permit)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return 0;
        }
    }

    /**
     * Get all superusers who have the given permission.
     *
     * @param string $name
     * @param string $password
     * @param int    $permit
     *
     * @return array|null
     */
    public function getLoginSuperuserWithPermit(string $name, string $password, int $permit): ?array
    {
        try
        {
            $qb = $this->createQueryBuilder('u');

            return $qb
                ->where('u.login = :name AND u.password = :password AND BIT_AND(u.superuser, :permit) > 0')
                ->setParameters([
                    'name' => $name,
                    'password' => $password,
                    'permit' => $permit
                ])
                ->setMaxResults(1)
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
