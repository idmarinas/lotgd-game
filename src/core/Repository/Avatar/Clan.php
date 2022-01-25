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

namespace Lotgd\Core\Repository\Avatar;

use Throwable;
use DateTime;
use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Entity as EntityCore;
use Tracy\Debugger;

/**
 * Functions for clan of characters.
 */
trait Clan
{
    /**
     * Get list of membership of clan.
     */
    public function getClanMembershipList(int $clanId): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.name', 'IDENTITY(u.acct) AS acctid', 'u.clanrank', 'u.clanjoindate', 'u.dragonkills', 'u.level')
                ->addSelect('a.laston', 'a.login')
                ->leftJoin('LotgdCore:User', 'a', Join::WITH, $query->expr()->eq('a.acctid', 'u.acct'))
                ->where('u.clanid = :clan')
                ->orderBy('u.clanrank', 'DESC')
                ->addOrderBy('u.dragonkills', 'DESC')
                ->addOrderBy('u.level', 'DESC')
                ->addOrderBy('u.clanjoindate')
                ->setParameter('clan', $clanId)
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
     * Get details of membership of clan.
     */
    public function getClanMembershipDetails(int $clanId): ?array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('COUNT(1) AS count', 'u.clanrank')
                ->where('u.clanid = :clan')
                ->groupBy('u.clanrank')
                ->orderBy('u.clanrank', 'DESC')
                ->setParameter('clan', $clanId)
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
     * Count leaders of a clan.
     */
    public function getClanLeadersCount(int $clanId): int
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('COUNT(1)')
                ->where('u.clanid = :clan AND u.clanrank >= :rank')
                ->setParameter('clan', $clanId)
                ->setParameter('rank', CLAN_LEADER)
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

    /**
     * Get one member of clan that can be promote to leader.
     */
    public function getViableLeaderForClan(int $clanId, int $acctId = 0): ?array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $query->select('u.id', 'u.name', 'IDENTITY(u.acct) AS acctid', 'u.clanrank', 'u.sex')
                ->where('u.clanid = :clan AND u.clanrank > :rank')
                ->setParameter('clan', $clanId)
                ->setParameter('rank', CLAN_APPLICANT)
                ->orderBy('u.clanrank', 'DESC')
                ->orderBy('u.clanjoindate', 'DESC')
            ;

            if ($acctId !== 0)
            {
                $query->andWhere('u.acct != :acct')
                    ->setParameter('acct', $acctId)
                ;
            }

            return $query->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Get leaders of clan exclude select acctId.
     *
     * @param int $acctId Exclude this acctId
     */
    public function getLeadersFromClan(int $clanId, int $acctId = 0): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $query->select('u.id', 'u.name', 'IDENTITY(u.acct) AS acctid', 'u.clanrank', 'u.sex')
                ->where('u.clanid = :clan AND u.clanrank >= :rank')
                ->setParameter('clan', $clanId)
                ->setParameter('rank', CLAN_OFFICER)
            ;

            if ($acctId !== 0)
            {
                $query->andWhere('u.acct != :acct')
                    ->setParameter('acct', $acctId)
                ;
            }

            return $query->getQuery()
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
     * Set new leader for a clan.
     */
    public function setNewClanLeader(int $characterId): int
    {
        try
        {
            $query = $this->_em->createQueryBuilder()->update($this->_entityName, 'u');

            return $query
                ->set('u.clanrank', '?1')
                ->where('u.id = :char')
                ->setParameter(1, CLAN_LEADER)
                ->setParameter('char', $characterId)
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

    /**
     * Get information of character with acctId and rank less or equal to.
     *
     * @return object|null
     */
    public function getCharacterFromAcctidAndRank(int $acctId, int $rank)
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u')
                ->where('u.acct = :acct AND u.clanrank <= :rank')
                ->setParameter('acct', $acctId)
                ->setParameter('rank', $rank)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);
        }
    }

    /**
     * Expel characters from a deleted clan.
     */
    public function expelPlayersFromDeletedClan(int $clanId): int
    {
        $query = $this->_em->createQueryBuilder()->update($this->_entityName, 'u');

        try
        {
            return $query
                ->set('u.clanid', 0)
                ->set('u.clanrank', CLAN_APPLICANT)
                ->set('u.clanjoindate', new DateTime('0000-00-00 00:00:00'))
                ->where('u.clanid = :clan')
                ->setParameter('clan', $clanId)
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
