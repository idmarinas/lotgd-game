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

namespace Lotgd\Bundle\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Bundle\CoreBundle\Entity\Referers;

class ReferersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Referers::class);
    }

    /**
     * Delte old referers in data base.
     */
    public function deleteExpireReferers(int $expire): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval("P{$expire}D"));

            return $query->delete($this->_entityName, 'u')
                ->where('u.last < :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            return 0;
        }
    }
}
