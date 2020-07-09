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

class CompanionsRepository extends DoctrineRepository
{
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
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Find one by id.
     * Entity is translated.
     *
     * @return array|null
     */
    public function findOneCompanionById(int $id)
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
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Get an array by ids.
     * Entities is translated.
     *
     * @return array|null
     */
    public function findCompanionsById(array $ids)
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
        catch (\Throwable $th)
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
            $query->orderBy('u.category', 'DESC');
            $query->addOrderBy('u.name', 'DESC');

            $query = $this->createTranslatebleQuery($query);

            return $query->getResult();
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
