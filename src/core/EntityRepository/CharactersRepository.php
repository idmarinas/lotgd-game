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
use Tracy\Debugger;

class CharactersRepository extends DoctrineRepository
{
    use Character\Bio;
    use Character\Clan;
    use Character\Setting;

    /**
     * Get character's name from an account ID.
     *
     * @param int $acctId
     *
     * @return string
     */
    public function getCharacterNameFromAcctId(int $acctId): ?string
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            return $qb->select('u.name')
                ->where('u.acct = :acct')
                ->setParameters(['acct' => $acctId])
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Get a list of characters with similar name.
     *
     * @param string $name
     * @param int    $limit
     *
     * @return array
     */
    public function findLikeName(string $name, int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('u');

        try
        {
            return $qb->where('u.name LIKE :name')
                ->setParameter('name', $name)
                ->setMaxResults($limit)
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

    /**
     * Get info of character for PvP.
     *
     * @param int $characterId
     *
     * @return array|null
     */
    public function getCharacterForPvp(int $characterId): ?array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.name AS creaturename', 'u.level AS creaturelevel', 'u.weapon AS creatureweapon', 'u.dragonkills', 'u.gold AS creaturegold', 'u.experience AS creatureexp', 'u.maxhitpoints AS creaturemaxhealth', 'u.hitpoints AS creaturehealth', 'u.attack AS creatureattack', 'u.defense AS creaturedefense', 'u.location', 'u.alive', 'u.pvpflag', 'u.boughtroomtoday', 'u.race')
                ->addSelect('a.loggedin', 'a.laston', 'a.acctid')
                ->leftJoin('LotgdCore:Accounts', 'a', 'WITH', $query->expr()->eq('a.acctid', 'u.acct'))
                ->where('u.id = :char')
                ->setParameter('char', $characterId)

                ->getQuery()
                ->getSingleResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }
}
