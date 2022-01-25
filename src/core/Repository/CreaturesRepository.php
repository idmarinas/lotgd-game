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
use Lotgd\Core\Entity\Creatures;
use Tracy\Debugger;

class CreaturesRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Creatures::class);
    }

    /**
     * Find one creature by id.
     * Creature is translated.
     */
    public function findOneCreatureById(int $id): ?array
    {
        try
        {
            $dql = 'SELECT a
                FROM LotgdCore:Creatures a
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
     * Get an array of creatures by ids.
     * Creatures is translated.
     */
    public function findCreaturesById(array $ids): ?array
    {
        try
        {
            $dql = 'SELECT a
                FROM LotgdCore:Creatures a
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
}
