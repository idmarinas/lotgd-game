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

use Lotgd\Core\Repository\AccountsOutput\Backup;
use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\AccountsOutput as AccountsOutputEntity;
use Tracy\Debugger;

class AccountsOutputRepository extends ServiceEntityRepository
{
    use Backup;
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountsOutputEntity::class);
    }

    /**
     * Get output code for account.
     */
    public function getOutput(int $acctId): string
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.output')
                ->where('u.acctid = :acct')
                ->setParameter('acct', $acctId)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return '';
        }
    }

    /**
     * Delete output of account.
     */
    public function deleteOutputOfAccount(int $accountId): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.acctid = :acct')
                ->setParameter('acct', $accountId)
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
