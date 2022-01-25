<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\Repository;

use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Masters;
use Tracy\Debugger;

class MastersRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Masters::class);
    }

    /**
     * Find one master by id.
     * Master is translated.
     */
    public function findOneMasterById(int $id): ?array
    {
        try
        {
            $dql = "SELECT a
                FROM {$this->_entityName} a
                WHERE a.creatureid = :id
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
     * Get an array of masters by ids.
     * Masters is translated.
     */
    public function findMastersById(array $ids): ?array
    {
        try
        {
            $dql = "SELECT a
                FROM {$this->_entityName} a
                WHERE a.creatureid IN (:id)
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
}
