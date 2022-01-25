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

namespace Lotgd\Core\Repository;

use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Armor;
use Tracy\Debugger;

class ArmorRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Armor::class);
    }

    /**
     * Get de max level of armors.
     *
     * @param int $dragonKills
     */
    public function getMaxArmorLevel(?int $dragonKills = null): int
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $query->select('MAX(u.level)');

            if (\is_int($dragonKills))
            {
                $query->where('u.level <= :lvl')
                    ->setParameters(['lvl' => $dragonKills])
                ;
            }

            return $query->setMaxResults(1)
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
     * Get next defense in level of armors.
     */
    public function getNextDefenseLevel(int $level): int
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('MAX(u.defense+1)')
                ->where('u.level = :lvl')
                ->setParameters(['lvl' => $level])
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return 1;
        }
    }

    /**
     * Get a translated list for a level.
     *
     * @return array
     */
    public function findByLevel(int $level): ?array
    {
        try
        {
            $dql = 'SELECT a
                FROM LotgdCore:Armor a
                WHERE a.level = :lvl
                ORDER BY a.defense ASC
            ';

            $query = $this->createTranslatebleQuery($dql);
            $query->setParameter('lvl', $level);

            return $query->getArrayResult();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Find one by id.
     * Entity is translated.
     */
    public function findOneArmorById(int $id): ?array
    {
        try
        {
            $dql = 'SELECT a
                FROM LotgdCore:Armor a
                WHERE a.armorid = :id
            ';

            $query = $this->createTranslatebleQuery($dql);
            $query->setParameter('id', $id);

            return $query->getArrayResult()[0];
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Get an array by ids.
     * Entities is translated.
     */
    public function findArmorsById(array $ids): ?array
    {
        try
        {
            $dql = 'SELECT a
                FROM LotgdCore:Armor a
                WHERE a.armorid IN (:id)
            ';

            $query = $this->createTranslatebleQuery($dql);
            $query->setParameter('id', $ids);

            return $query->getArrayResult();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }
}
