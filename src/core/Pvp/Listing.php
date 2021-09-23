<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/public/LICENSE.txt
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Pvp;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Lotgd\Core\Lib\Settings;

class Listing
{
    protected $doctrine;
    /** @var \Lotgd\Core\Repository\CharactersRepository */
    protected $repository;
    protected $query;
    private $settings;

    public function __construct(EntityManagerInterface $doctrine, Settings $settings)
    {
        $this->doctrine = $doctrine;
        $this->settings = $settings;
    }

    /**
     * Get a list of players available for pvp.
     *
     * @return Paginator
     */
    public function getPvpList(?string $location = null)
    {
        $qr = clone $this->getQuery();

        $qr->orderBy('u.location', 'DESC')
            ->addOrderBy('u.level', 'DESC')
            ->addOrderBy('u.experience', 'DESC')
            ->addOrderBy('u.dragonkills', 'DESC')
        ;

        if ($location)
        {
            $qr->andWhere('u.location = :loc')
                ->setParameter('loc', $location)
            ;
        }

        return $this->repository->getPaginator($qr);
    }

    /**
     * Get a list of sleepers in each location.
     *
     * @return array
     */
    public function getLocationSleepersCount(?string $location = null)
    {
        $qr = clone $this->getQuery();

        $qr->select('count(u.location) AS sleepers', 'u.location')

            ->groupBy('u.location')

            ->orderBy('u.location', 'DESC')
        ;

        if ($location)
        {
            $qr->andWhere('u.location != :loc')
                ->setParameter('loc', $location)
            ;
        }

        return $qr->getQuery()->getResult();
    }

    /**
     * Get query for list of PvP.
     *
     * @return QueryBuilder
     */
    public function getQuery()
    {
        global $session;

        if ( ! $this->query)
        {
            $days      = $this->settings->getSetting('pvpimmunity', 5);
            $exp       = $this->settings->getSetting('pvpminexp', 1500);
            $levdiff   = $this->settings->getSetting('pvprange', 2);
            $pvpSameId = (bool) $this->settings->getSetting('pvpsameid', 0);
            $pvpSameIp = (bool) $this->settings->getSetting('pvpsameip', 0);

            $this->repository = $this->doctrine->getRepository('LotgdCore:Avatar');
            $this->query      = $this->repository->createQueryBuilder('u');
            $expr             = $this->query->expr();

            $this->query->select('u.id AS character_id', 'u.name', 'u.race', 'u.alive', 'u.location', 'u.sex', 'u.level', 'u.dragonkills', 'u.pvpflag', 'u.clanrank')
                ->addSelect('a.acctid', 'a.loggedin', 'a.login', 'a.laston')
                ->addSelect('c.clanshort', 'c.clanname')

                ->leftJoin('LotgdCore:User', 'a', 'WITH', $expr->eq('a.acctid', 'u.acct'))
                ->leftJoin('LotgdCore:Clans', 'c', 'WITH', $expr->eq('c.clanid', 'u.clanid'))

                ->where('a.acctid != :acct')
                ->andWhere('a.locked = 0')
                ->andWhere('a.loggedin = 0')
                ->andWhere('u.alive = 1')
                ->andWhere('u.slaydragon = 0')
                ->andWhere('u.age > :days OR u.dragonkills > 0 OR u.experience > :exp')

                ->setParameters([
                    'days' => $days,
                    'exp'  => $exp,
                    'acct' => $session['user']['acctid'],
                ])
            ;

            if (-1 == $levdiff)
            {
                $this->query->andWhere('u.level >= :lev1 AND u.level <= :lev2')
                    ->setParameter('lev1', $session['user']['level'] - 1)
                    ->setParameter('lev2', $session['user']['level'] + 2)
                ;
            }

            //-- Not allow same ID
            if ( ! $pvpSameId)
            {
                $this->query->andWhere('a.uniqueid != :sameid')
                    ->setParameter('sameid', $session['user']['uniqueid'])
                ;
            }

            //-- Not allow same IP
            if ( ! $pvpSameIp)
            {
                $this->query->andWhere('a.lastip != :sameip')
                    ->setParameter('sameip', $session['user']['lastip'])
                ;
            }
        }

        return $this->query;
    }

    /**
     * Set query for list of PvP.
     *
     * @return self
     */
    public function setQuery(QueryBuilder $query)
    {
        $this->query = $query;

        return $this;
    }
}
