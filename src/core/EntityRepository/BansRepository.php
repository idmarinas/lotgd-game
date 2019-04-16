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

class BansRepository extends DoctrineRepository
{
    /**
     * Remove expired bans.
     *
     * @return int
     */
    public function removeExpireBans(): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where("u.banexpire < :date AND u.banexpire > '0000-00-00 00:00:00'")
                ->setParameter('date', new \DateTime('now'))
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return 0;
        }
    }

    /**
     * Delete ban from data base.
     *
     * @param string $ip
     * @param string $id
     *
     * @return bool
     */
    public function deleteBan($ip, $id): int
    {
        $query = $this->_em->createQueryBuilder();
        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.ipfilter = :ip AND u.uniqueid = :id')
                ->setParameter('ip', $ip)
                ->setParameter('id', $id)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return 0;
        }
    }
}
