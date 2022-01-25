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

use DateTime;
use DateInterval;
use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Gamelog;
use Tracy\Debugger;

class GamelogRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gamelog::class);
    }

    /**
     * Delte old fail logs in data base.
     */
    public function deleteExpireGamelogs(int $expire): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new DateTime('now');
            $date->sub(new DateInterval("P{$expire}D"));

            return $query->delete($this->_entityName, 'u')
                ->where('u.date < :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->execute()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
