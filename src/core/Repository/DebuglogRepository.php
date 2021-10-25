<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Debuglog;
use Tracy\Debugger;

class DebuglogRepository extends ServiceEntityRepository implements RepositoryBackupInterface
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Debuglog::class);
    }

    /**
     * {@inheritDoc}
     */
    public function backupGetDataFromAccount(int $accountId): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->where('u.actor = :acct')

                ->setParameter('acct', $accountId)

                ->getQuery()
                ->getResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function backupDeleteDataFromAccount(int $accountId): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.actor = :acct')
                ->setParameter('acct', $accountId)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
