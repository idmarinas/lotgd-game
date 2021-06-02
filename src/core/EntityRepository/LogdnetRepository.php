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

namespace Lotgd\Core\EntityRepository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Lotgd\Core\Doctrine\ORM\EntityRepository as DoctrineRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tracy\Debugger;

class LogdnetRepository extends DoctrineRepository
{
    private $cache;

    public function __construct(CacheInterface $coreLotgdCache, EntityManagerInterface $em, Mapping\ClassMetadata $class)
    {
        $this->cache = $coreLotgdCache;

        parent::__construct($em, $class);
    }

    /**
     * Delete servers older than two weeks.
     */
    public function deletedOlderServer(): int
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

            return 0;
        }
    }

    /**
     * Degrade the popularity of any server which hasn't been updated in the past 5 minutes by 1%.
     * This means that unpopular servers will fall toward the bottom of the list.
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
                ->set('u.lastupdate', $query->expr()->literal((new \DateTime('now'))->format(\DateTime::ISO8601)))

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
     */
    public function getNetServerList(): array
    {
        $query = $this->createQueryBuilder('u');

        try
        {
            return $this->cache->get('logdnet-repository-'.__METHOD__, function (ItemInterface $item) use ($query)
            {
                $item->expiresAfter(1800); //-- Cache 1800 seconds (30 mins)

                $result = $query->select('u.address', 'u.description', 'u.version', 'u.admin', 'u.priority')
                    ->where('u.lastping > :date')
                    ->setParameter('date', (new \DateTime('now'))->sub(new \DateInterval('P2W')))
                    ->getQuery()
                    ->getArrayResult()
                ;

                return $this->applyLogdnetBans($result);
            });
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    private function applyLogdnetBans($logdnet)
    {
        $repository = $this->getDoctrine()->getRepository('LotgdCore:Logdnetbans');
        $entities   = $repository->findAll();

        foreach ($entities as $ban)
        {
            foreach ($logdnet as $key => $net)
            {
                $text = $ban->getBanvalue();
                if (\preg_match("/{$text}/i", $net[$ban->getBantype()]))
                {
                    unset($logdnet[$key]);
                }
            }
        }

        return $logdnet;
    }
}
