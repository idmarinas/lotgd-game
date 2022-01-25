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
use DateTime;
use DateInterval;
use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Entity as LotgdEntity;
use Tracy\Debugger;

/**
 * Functions for login/logout user.
 */
trait Login
{
    /**
     * Process login and get data.
     *
     * @return array
     */
    public function processLoginGetAcctData(string $login, string $password)
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            $data = $qb->addSelect('ep')
                ->where('u.login = :login AND u.password = :password AND u.locked = :locked')
                ->setParameters([
                    'login'    => $login,
                    'password' => $password,
                    'locked'   => false,
                ])
                ->leftJoin(LotgdEntity\AccountsEverypage::class, 'ep', Join::WITH, $qb->expr()->eq('ep.acctid', 'u.acctid'))
                ->getQuery()
                ->getResult()
            ;

            //-- Fail if not found
            if (0 == \count($data))
            {
                return null;
            }

            return $this->processUserData($data);
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Logout inactive accounts.
     */
    public function logoutInactiveAccounts(int $timeout): bool
    {
        try
        {
            $date = new DateTime('now');
            $date->sub(new DateInterval("PT{$timeout}S"));

            $query = $this->_em->createQueryBuilder();

            return $query->update($this->_entityName, 'u')
                ->set('u.loggedin', 0)
                ->where('u.laston <= :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->execute()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Get acctid from login.
     */
    public function getAcctIdFromLogin(string $login): int
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.acctid')
                ->where('u.login = :login')
                ->setParameter('login', $login)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
