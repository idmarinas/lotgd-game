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

use Lotgd\Core\Repository\ModuleUserprefs\Backup;
use Doctrine\Common\Collections\Criteria;
use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\ModuleUserprefs as ModuleUserprefsEntity;
use Tracy\Debugger;

class ModuleUserprefsRepository extends ServiceEntityRepository implements RepositoryBackupInterface
{
    use Backup;
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleUserprefsEntity::class);
    }

    /**
     * Find modules prefs for modules.
     */
    public function findModulesPrefs(array $modules, int $acctId): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query
                ->where('u.modulename IN (:modules) AND u.userid = :acct')
                ->andWhere("u.setting LIKE 'user_%' OR u.setting LIKE 'check_%'")

                ->setParameter('modules', $modules)
                ->setParameter('acct', $acctId)

                ->orderBy('u.modulename', Criteria::DESC)

                ->getQuery()
                ->getArrayResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
