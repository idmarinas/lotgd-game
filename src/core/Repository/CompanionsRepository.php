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
use Doctrine\Common\Collections\Criteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Companions;
use Tracy\Debugger;

class CompanionsRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Companions::class);
    }

    /**
     * Get a list of available mecenaries.
     */
    public function getMercenaryList(string $location, int $dragonKills): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $query
                ->where('u.companioncostdks <= :dk')
                ->andWhere("u.companionlocation = :loc OR u.companionlocation = 'all'")
                ->andWhere('u.companionactive = 1')
            ;

            $query = $this->createTranslatebleQuery($query);
            $query
                ->setParameter('dk', $dragonKills)
                ->setParameter('loc', $location)
            ;

            return $query->getArrayResult();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Find one by id.
     * Entity is translated.
     */
    public function findOneCompanionById(int $id): ?array
    {
        try
        {
            $dql = 'SELECT a
                FROM LotgdCore:Companion a
                WHERE a.creatureid = :id
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
    public function findCompanionsById(array $ids): ?array
    {
        try
        {
            $dql = 'SELECT a
                FROM LotgdCore:Companion a
                WHERE a.creatureid IN (:id)
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

    /**
     * Get list of companions.
     */
    public function getList(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $query->orderBy('u.category', Criteria::DESC);
            $query->addOrderBy('u.name', Criteria::DESC);

            $query = $this->createTranslatebleQuery($query);

            return $query->getResult();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
