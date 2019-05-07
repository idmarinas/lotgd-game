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

namespace Lotgd\Core\EntityRepository;

use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Lotgd\Core\Entity as EntityCore;
use Tracy\Debugger;

class ClansRepository extends DoctrineRepository
{
    /**
     * Get list of clans for apply to enter.
     *
     * @param int $order
     *
     * @return array
     */
    public function getClanListWithMembersCount(int $order): array
    {
        $query = $this->createQueryBuilder('u');
        $countQuery = $this->_em->createQueryBuilder()->from(EntityCore\Characters::class, 'c');

        try
        {
            $countQuery->select('COUNT(1)')
                ->where('c.clanrank >= :rank AND c.clanid = u.clanid')
            ;

            $query->select('u.clanname', 'u.clanid', 'u.clanshort', 'u.clandesc')
                ->addSelect('('.$countQuery->getDQL().') AS members')
                ->setParameter('rank', CLAN_APPLICANT)
            ;

            $query->orderBy('members', 'DESC');

            if (1 == $order)
            {
                $query->orderBy('u.clanname', 'ASC');
            }
            elseif (2 == $order)
            {
                $query->orderBy('u.clanshort', 'ASC');
            }

            return $query
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
     * Create a new clan.
     *
     * @param array $data
     *
     * @return int|null
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
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return null;
        }
    }
}
