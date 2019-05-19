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
use Lotgd\Core\Entity as EntityCore;
use Tracy\Debugger;

/**
 * Functions for characters from account.
 */
trait Character
{
    /**
     * Get information of character for bio page.
     *
     * @param int $account
     *
     * @return array
     */
    public function getCharacterInfoFromAcctId(int $account): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('ch.name', 'ch.level', 'ch.sex', 'ch.title', 'ch.specialty', 'ch.hashorse', 'ch.resurrections', 'ch.bio', 'ch.dragonkills', 'ch.race', 'ch.clanrank')
                ->addSelect('u.acctid', 'u.laston', 'u.loggedin')
                ->addSelect('c.clanid', 'c.clanname', 'c.clanshort')
                ->addSelect('m.mountname')
                ->leftJoin(EntityCore\Characters::class, 'ch', Join::WITH, $query->expr()->eq('ch.id', 'u.character'))
                ->leftJoin(EntityCore\Clans::class, 'c', Join::WITH, $query->expr()->eq('c.clanid', 'ch.clanid'))
                ->leftJoin(EntityCore\Mounts::class, 'm', Join::WITH, $query->expr()->eq('m.mountid', 'ch.hashorse'))
                ->where('u.acctid = :acct')
                ->setParameter('acct', $account)
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
     * Get recent news from account and character.
     *
     * @param int $account
     *
     * @return array
     */
    public function getCharacterNewsFromAcctId(int $account): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('n')
                ->leftJoin(EntityCore\News::class, 'n', Join::WITH, $query->expr()->eq('n.accountId', 'u.acctid'))
                ->where('u.acctid = :acct')
                ->setParameter('acct', $account)
                ->setMaxResults(100)
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
}
