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
     *
     * @param string $location
     * @param int    $dragonKills
     *
     * @return array
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

            $query = $this->createTranslatebleCompanionQuery($query);
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

            $query = $this->createTranslatebleCompanionQuery($dql);
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

            $query = $this->createTranslatebleCompanionQuery($dql);
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
     * Create query for translate entity.
     *
     * @param string $dql
     * Note: If pass a "Doctrine\ORM\QueryBuilder" auto-get a DQL string.
     *
     * @return \Doctrine\ORM\Query
     */
    public function createTranslatebleCompanionQuery(string $dql)
    {
        $query = $this->_em->createQuery($dql);

        $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        // take locale from session or request etc.
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, \Locale::getDefault());
        // fallback to default values in case if record is not translated
        $query->setHint(TranslatableListener::HINT_FALLBACK, 1);

        return $query;
    }
}
