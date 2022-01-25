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

use Doctrine\Common\Collections\Criteria;
use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Clans;
use Tracy\Debugger;

class ClansRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clans::class);
    }

    /**
     * Get list of clans for apply to enter.
     */
    public function getClanListWithMembersCount(int $order): array
    {
        $query      = $this->createQueryBuilder('u');
        $countQuery = $this->_em->createQueryBuilder()->from('LotgdCore:Avatar', 'c');

        try
        {
            $countQuery->select('COUNT(1)')
                ->where('c.clanrank >= :rank AND c.clanid = u.clanid')
            ;

            $query->select('u.clanname', 'u.clanid', 'u.clanshort', 'u.clandesc')
                ->addSelect('('.$countQuery->getDQL().') AS members')
                ->setParameter('rank', CLAN_APPLICANT)
            ;

            $query->orderBy('members', Criteria::DESC);

            if (1 == $order)
            {
                $query->orderBy('u.clanname', Criteria::ASC);
            }
            elseif (2 == $order)
            {
                $query->orderBy('u.clanshort', Criteria::ASC);
            }

            return $query
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

    /**
     * Create a new clan.
     */
    public function createClan(array $data): ?int
    {
        try
        {
            $entity = $this->hydrateEntity($data);

            $this->_em->persist($entity);
            $this->_em->flush();

            return $entity->getClanid();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }
}
