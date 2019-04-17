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
use Zend\Hydrator\ClassMethods;

class AccountsRepository extends DoctrineRepository
{
    use Account\Bans;
    use Account\Character;
    use Account\Login;
    use Account\Superuser;
    use Account\User;

    /**
     * Get data of user by ID of account.
     *
     * @param int $acctId
     *
     * @return array
     */
    public function getUserById(int $acctId)
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            $data = $qb->addSelect('ep')
                ->where('u.acctid = :acctid')
                ->setParameter('acctid', $acctId)
                ->leftJoin(LotgdEntity\AccountsEverypage::class, 'ep', Join::WITH, $qb->expr()->eq('ep.acctid', 'u.acctid'))
                ->getQuery()
                ->getResult()
            ;
        }
        catch (\Throwable $th)
        {
            return null;
        }

        //-- Fail if not found
        if (0 == count($data))
        {
            \Tracy\Debugger::log($th);

            return null;
        }

        return $this->processUserData($data);
    }

    /**
     * Get total accounts that are online.
     *
     * @param int $timeout
     *
     * @return string
     */
    public function getCountAcctsOnline(int $timeout): int
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval("PT{$timeout}S"));

            return (int) $qb->select('COUNT(1)')
                ->where('u.locked = 0 AND u.loggedin = 1 AND u.laston > :date')
                ->setParameter('date', $date)
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
     * Private function to process data of user.
     *
     * @param array $data
     *
     * @return array
     */
    private function processUserData(array $data)
    {
        $hydrator = new ClassMethods();

        $account = $hydrator->extract($data[0]);
        $character = $hydrator->extract($account['character']);

        if (! $data[1])
        {
            $data[1] = new LotgdEntity\AccountsEverypage();
            $data[1]->setAcctid($account['acctid']);
        }

        $everypage = $hydrator->extract($data[1]);

        $character['character_id'] = $character['id'];

        unset($account['character']);

        return array_merge($account, $character, $everypage);
    }
}
