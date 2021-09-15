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

namespace Lotgd\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Avatar as AvatarEntity;
use Tracy\Debugger;

class AvatarRepository extends ServiceEntityRepository
{
    use Avatar\Bio;
    use Avatar\Clan;
    use Avatar\Setting;
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvatarEntity::class);
    }

    /**
     * Get character's name from an account ID.
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
     * Search by character name and account login.
     */
    public function findLikeName(string $name, int $limit = 100): array
    {
        $qb    = $this->createQueryBuilder('u');
        $query = $this->_em->createQueryBuilder();

        try
        {
            $character = $qb
                ->select('u.name', 'IDENTITY(u.acct) AS acctid', 'u.id', 'u.level', 'a.login', 'a.superuser', 'a.loggedin')
                ->leftJoin('LotgdCore:User', 'a', 'with', $qb->expr()->eq('a.avatar', 'u.id'))
                ->where('u.name LIKE :name')
                ->setParameter('name', "%{$name}%")
                ->setMaxResults($limit)
                ->getQuery()
                ->getArrayResult()
            ;

            $account = $query->from('LotgdCore:User', 'u')
                ->select('c.name', 'IDENTITY(c.acct) AS acctid', 'c.level', 'u.login', 'u.superuser', 'u.loggedin')
                ->leftJoin('LotgdCore:Avatar', 'c', 'with', $qb->expr()->eq('c.id', 'u.avatar'))
                ->where('u.login LIKE :name AND u.acctid NOT IN (:acct)')
                ->setParameter('name', "%{$name}%")
                ->setParameter('acct', array_map(function ($val)
                {
                    return $val['acctid'];
                }, $character))
                ->setMaxResults($limit - \count($character))
                ->getQuery()
                ->getArrayResult()
            ;

            return array_merge($character, $account);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Get info of character for PvP.
     */
    public function getCharacterForPvp(int $characterId): ?array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.id AS character_id', 'u.name AS creaturename', 'u.level AS creaturelevel', 'u.weapon AS creatureweapon', 'u.dragonkills', 'u.gold AS creaturegold', 'u.experience AS creatureexp', 'u.maxhitpoints AS creaturemaxhealth', 'u.hitpoints AS creaturehealth', 'u.attack AS creatureattack', 'u.defense AS creaturedefense', 'u.location', 'u.alive', 'u.pvpflag', 'u.boughtroomtoday', 'u.race')
                ->addSelect('a.loggedin', 'a.laston', 'a.acctid')
                ->leftJoin('LotgdCore:User', 'a', 'WITH', $query->expr()->eq('a.acctid', 'u.acct'))
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
