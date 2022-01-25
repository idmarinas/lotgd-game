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
use Doctrine\Common\Collections\Criteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Mounts;
use Tracy\Debugger;

class MountsRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mounts::class);
    }

    /**
     * Get list of mounts with owners.
     */
    public function getList(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $query->select('u.mountid', 'u.mountname', 'u.mountactive', 'u.mountcategory', 'u.mountforestfights', 'u.mountdkcost', 'u.mountcostgems', 'u.mountcostgold')
                ->addSelect('count(c.hashorse) AS owners')

                ->leftJoin('LotgdCore:Avatar', 'c', 'WITH', $query->expr()->eq('c.hashorse', 'u.mountid'))

                ->groupBy('u.mountid')
            ;

            $query = $this->createTranslatebleQuery($query);

            return $query->getResult();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Refund cost of mount to players.
     *
     * @param object $entity
     */
    public function refundMount($entity): bool
    {
        try
        {
            $query = $this->_em->createQuery("UPDATE LotgdCore:Avatar u SET u.gems = u.gems+?2, u.goldinbank = u.goldinbank+?3, u.hashorse = '0' WHERE u.hashorse = ?1");

            $query->setParameter(1, $entity->getMountid())
                ->setParameter(2, $entity->getMountcostgems())
                ->setParameter(3, $entity->getMountcostgold())
                ->execute()
            ;

            return true;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Get mounts by location (include all).
     */
    public function getMountsByLocation(string $location): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            $query
                ->where('u.mountactive = 1')
                ->andWhere('u.mountlocation = :all OR u.mountlocation = :loc')

                ->orderBy('u.mountcategory', Criteria::ASC)
                ->addOrderBy('u.mountcostgems', Criteria::ASC)
                ->addOrderBy('u.mountcostgold', Criteria::ASC)
            ;

            $query = $this->createTranslatebleQuery($query);
            $query->setParameter('all', 'all')
                ->setParameter('loc', $location)
            ;

            return $query->getResult();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    /**
     * Find one mount by id.
     * Mount is translated.
     */
    public function findOneMountById(int $id): ?array
    {
        try
        {
            $dql = "SELECT a
                FROM {$this->_entityName} a
                WHERE a.mountid = :id
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
     * Get an array of mounts by ids.
     * Mounts is translated.
     */
    public function findMountsById(array $ids): ?array
    {
        try
        {
            $dql = "SELECT a
                FROM {$this->_entityName} a
                WHERE a.mountid IN (:id)
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
