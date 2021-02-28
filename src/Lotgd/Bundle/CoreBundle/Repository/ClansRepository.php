<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Bundle\CoreBundle\Entity as EntityCore;
use Lotgd\Bundle\CoreBundle\Entity\Clans;
use Doctrine\Common\Collections\Criteria;

class ClansRepository extends ServiceEntityRepository
{
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
        $countQuery = $this->_em->createQueryBuilder()->from(EntityCore\Avatar::class, 'c');

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
        catch (\Throwable $th)
        {
            return [];
        }
    }
}
