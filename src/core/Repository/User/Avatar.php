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
use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Entity as EntityCore;
use Tracy\Debugger;

/**
 * Functions for characters from account.
 */
trait Avatar
{
    /**
     * Get information of character for bio page.
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
                ->leftJoin('LotgdCore:Avatar', 'ch', Join::WITH, $query->expr()->eq('ch.id', 'u.avatar'))
                ->leftJoin(EntityCore\Clans::class, 'c', Join::WITH, $query->expr()->eq('c.clanid', 'ch.clanid'))
                ->leftJoin(EntityCore\Mounts::class, 'm', Join::WITH, $query->expr()->eq('m.mountid', 'ch.hashorse'))
                ->where('u.acctid = :acct')
                ->setParameter('acct', $account)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Get recent news from account and character.
     */
    public function getCharacterNewsFromAcctId(int $account): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $results = $query->select('n')
                ->leftJoin(EntityCore\News::class, 'n', Join::WITH, $query->expr()->eq('n.accountId', 'u.acctid'))
                ->where('u.acctid = :acct')
                ->setParameter('acct', $account)
                ->setMaxResults(100)
                ->getQuery()
                ->getArrayResult()
            ;

            return ! empty($results) && is_array($results[0]) ? $results : [];
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Get character name from account ID.
     */
    public function getCharacterNameFromAcctId(int $account): string
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query
                ->select('c.name')
                ->leftJoin('LotgdCore:Avatar', 'c', 'with', $query->expr()->eq('c.acct', 'u.acctid'))
                ->where('u.acctid = :acct')

                ->setParameter('acct', $account)

                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return '';
        }
    }
}
