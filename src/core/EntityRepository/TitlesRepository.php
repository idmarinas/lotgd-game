<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.md
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Tracy\Debugger;

class TitlesRepository extends DoctrineRepository
{
    /**
     * Find one title by id.
     * Title is translated.
     *
     * @return array|null
     */
    public function findOneTitleById(int $id)
    {
        try
        {
            $dql = "SELECT a
                FROM {$this->_entityName} a
                WHERE a.titleid = :id
            ";

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
     * Get an array of titles by ids.
     * Titles is translated.
     *
     * @return array|null
     */
    public function findTitlesById(array $ids)
    {
        try
        {
            $dql = "SELECT a
                FROM {$this->_entityName} a
                WHERE a.titleid IN (:id)
            ";

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
}
