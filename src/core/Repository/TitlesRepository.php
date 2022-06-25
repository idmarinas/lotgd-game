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

namespace Lotgd\Core\Repository;

use Doctrine\Common\Collections\Criteria;
use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Titles;
use Tracy\Debugger;

class TitlesRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Titles::class);
    }

    /**
     * Find one title by id.
     * Title is translated.
     */
    public function findOneTitleById(int $id): ?array
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
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Get an array of titles by ids.
     * Titles is translated.
     */
    public function findTitlesById(array $ids): ?array
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
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }

    /**
     * Get list of titles.
     */
    public function getList(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $query->orderBy('u.dk', Criteria::ASC);

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
