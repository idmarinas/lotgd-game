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

use Lotgd\Core\Repository\Module\Setting;
use Throwable;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Modules;
use Tracy\Debugger;

class ModulesRepository extends ServiceEntityRepository
{
    use Setting;
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modules::class);
    }

    /**
     * Restart filemoddate to default value.
     */
    public function reinstallModule(string $module): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->update($this->_entityName, 'u')
                ->set('u.filemoddate', ':date')
                ->where('u.modulename = :module')

                ->setParameter('module', $module)
                ->setParameter('date', '0000-00-00 00:00:00')

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

    /**
     * Find modules with this info key.
     */
    public function findInfoKeyLike(string $key): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.modulename')
                ->where('u.infokeys LIKE :key AND u.active = 1')

                ->setParameter('key', "%|{$key}|%")

                ->orderBy('u.modulename', Criteria::DESC)

                ->getQuery()
                ->getResult()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
