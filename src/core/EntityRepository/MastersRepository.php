<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\EntityRepository;

use Gedmo\Translatable\TranslatableListener;
use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Tracy\Debugger;

class MastersRepository extends DoctrineRepository
{
    /**
     * Find one master by id.
     * Master is translated.
     *
     * @return array|null
     */
    public function findOneMasterById(int $id)
    {
        try
        {
            $dql = "SELECT a
                FROM {$this->_entityName} a
                WHERE a.creatureid = :id
            ";

            $query = $this->createTranslatebleMasterQuery($dql);
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
     * Get an array of masters by ids.
     * Masters is translated.
     *
     * @return array|null
     */
    public function findMastersById(array $ids)
    {
        try
        {
            $dql = "SELECT a
                FROM {$this->_entityName} a
                WHERE a.creatureid IN (:id)
            ";

            $query = $this->createTranslatebleMasterQuery($dql);
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
     * Create query for translate master.
     *
     * @param string $dql
     * Note: If pass a "Doctrine\ORM\QueryBuilder" auto-get a DQL string.
     *
     * @return \Doctrine\ORM\Query
     */
    public function createTranslatebleMasterQuery(string $dql)
    {
        $query = $this->_em->createQuery($dql);

        $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        // take locale from session or request etc.
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, \Locale::getDefault());
        // fallback to default values in case if record is not translated
        $query->setHint(TranslatableListener::HINT_FALLBACK, 1);

        return $query;
    }

    /**
     * Alias for createTranslatebleMasterQuery.
     *
     * @param string $dql
     */
    public function createTranslatebleQuery(string $dql)
    {
        return $this->createTranslatebleMasterQuery($dql);
    }
}
