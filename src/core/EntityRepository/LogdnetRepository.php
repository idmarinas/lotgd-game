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
use Tracy\Debugger;

class LogdnetRepository extends DoctrineRepository
{
    /**
     * Delete servers older than two weeks.
     *
     * @return bool
     */
    public function deletedOlderServer(): bool
    {
        $date = new \DateTime('now');
        $date->sub(new \DateInterval('P2W'));

        $query = $this->_em->createQueryBuilder();

        try
        {
            return $query->delete($this->_entityName, 'u')
                ->where('u.lastping < :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Degrade the popularity of any server which hasn't been updated in the past 5 minutes by 1%.
     * This means that unpopular servers will fall toward the bottom of the list.
     *
     * @return int
     */
    public function degradePopularity(): int
    {
        $query = $this->_em->createQueryBuilder();

        try
        {
            $date = new \DateTime('now');
            $date->sub(new \DateInterval('PT5M'));

            return $query->update($this->_entityName, 'u')

                ->set('u.priority', 'u.priority * 0.99')
                ->set('u.lastupdate', (new \DateTime('now'))->format(\DateTime::ISO8601))

                ->where('u.lastupdate < :date')
                ->setParameter('date', $date)
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

    /**
     * Get our list of servers.
     *
     * @return array
     */
    public function getNetServerList(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $query->select('u.address', 'u.description', 'u.version', 'u.admin', 'u.priority')
                ->where('u.lastping > :date')
                ->setParameter('date', (new \DateTime('now'))->sub(new \DateInterval('P2W')))
                ->getQuery()
                ->getArrayResult()
            ;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }
}
