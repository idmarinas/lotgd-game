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

use DateTime;
use DateInterval;
use Throwable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lotgd\Core\Doctrine\ORM\EntityRepositoryTrait;
use Lotgd\Core\Entity\Logdnet;
use Lotgd\Core\Installer\Pattern\Version;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tracy\Debugger;

class LogdnetRepository extends ServiceEntityRepository
{
    use EntityRepositoryTrait;
    use Version;

    private $cache;

    public function __construct(ManagerRegistry $registry, CacheInterface $cache)
    {
        parent::__construct($registry, Logdnet::class);

        $this->cache = $cache;
    }

    /**
     * Delete servers older than two weeks.
     */
    public function deletedOlderServer(): int
    {
        $date = new DateTime('now');
        $date->sub(new DateInterval('P2W'));

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
        catch (Throwable $th)
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
            $date = new DateTime('now');
            $date->sub(new DateInterval('PT5M'));

            return $query->update($this->_entityName, 'u')

                ->set('u.priority', 'u.priority * 0.99')
                ->set('u.lastupdate', $query->expr()->literal((new DateTime('now'))->format(DateTime::ISO8601)))

                ->where('u.lastupdate < :date')
                ->setParameter('date', $date)
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
                    ->setParameter('date', (new DateTime('now'))->sub(new DateInterval('P2W')))
                    ->getQuery()
                    ->getArrayResult()
                ;

                $result = $this->applyLogdnetBans($result);

                usort($result, [$this, 'lotgdSort']);

                return $result;
            });
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return [];
        }
    }

    private function applyLogdnetBans($logdnet)
    {
        $repository = $this->_em->getRepository('LotgdCore:Logdnetbans');
        $entities   = $repository->findAll();

        foreach ($entities as $ban)
        {
            foreach ($logdnet as $key => $net)
            {
                $text = $ban->getBanvalue();
                if (preg_match("/{$text}/i", $net[$ban->getBantype()]))
                {
                    unset($logdnet[$key]);
                }
            }
        }

        return $logdnet;
    }

    private function lotgdSort($a, $b)
    {
        $official_prefixes = $this->getFullListOfVersion();

        unset($official_prefixes['Clean Install']);
        $official_prefixes = array_keys($official_prefixes);

        $aver = strtolower(str_replace(' ', '', $a['version']));
        $bver = strtolower(str_replace(' ', '', $b['version']));

        // Okay, if $a and $b are the same version, use the priority
        // This is true whether or not they are the official version or not.
        // We bubble the official version to the top below.
        if (0 == strcmp($aver, $bver))
        {
            if ($a['priority'] == $b['priority'])
            {
                return 0;
            }

            return ($a['priority'] < $b['priority']) ? 1 : -1;
        }

        // Unknown versions are always worse than non-unknown
        if (0 == strcmp($aver, 'unknown') && 0 != strcmp($bver, 'unknown'))
        {
            return 1;
        }
        elseif (0 == strcmp($bver, 'unknown') && 0 != strcmp($aver, 'unknown'))
        {
            return -1;
        }

        // Check if either of them are a prefix.
        $costa = 10000;
        $costb = 10000;

        foreach ($official_prefixes as $index => $value)
        {
            if (0 == strncmp($aver, $value, \strlen($value)) && 10000 == $costa)
            {
                $costa = $index;
            }

            if (0 == strncmp($bver, $value, \strlen($value)) && 10000 == $costb)
            {
                $costb = $index;
            }
        }

        // If both are the same prefix (or no prefix), just strcmp.
        if ($costa === $costb)
        {
            return strcmp($aver, $bver);
        }

        return ($costa < $costb) ? -1 : 1;
    }
}
