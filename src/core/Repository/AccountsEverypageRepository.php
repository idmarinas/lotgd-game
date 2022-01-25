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
use Lotgd\Core\Entity\AccountsEverypage;
use Tracy\Debugger;

class AccountsEverypageRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountsEverypage::class);
    }

    /**
     * Get stats of page gen.
     */
    public function getStatsPageGen(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('SUM(u.gentime) AS gentime', 'SUM(u.gentimecount) AS gentimecount', 'SUM(u.gensize) AS gensize')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
