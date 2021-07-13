<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class HofController extends AbstractController
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    public function index(array $params, Request $request): Response
    {
        $op    = (string) $request->query->get('op');
        $subop = (string) $request->query->get('subop');
        $page  = $request->query->getInt('page');
        $subop = $subop ?: 'best';
        $op    = $op ?: 'kills';
        $order = ('worst' == $subop) ? 'ASC' : 'DESC';

        $method = 'hof'.\ucfirst($op);
        $method = \method_exists($this, $method) ? $method : 'hofKills';

        $params['page']  = $page;
        $params['order'] = $order;
        $params['subop'] = $subop;

        return $this->$method($params, $order, $subop);
    }

    protected function hofDays(array $params): Response
    {
        global $session;

        $params['tpl'] = 'days';
        $subop         = $params['subop'];
        $order         = ('worst' == $subop) ? 'DESC' : 'ASC';
        $query         = $this->getQuery();

        $query->select('c.name', 'c.bestdragonage')
            ->andWhere('c.dragonkills > 0')
            ->andWhere('c.bestdragonage > 0')
            ->orderBy('c.bestdragonage', $order)
            ->addOrderBy('c.level', $order)
            ->addOrderBy('c.experience', $order)
        ;

        $me = clone $query;
        $me->select('count(1) AS count')
            ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.dragonkills > 0 AND c.bestdragonage <= ?0')
            ->setParameters([
                0        => $session['user']['bestdragonage'],
                'permit' => SU_HIDE_FROM_LEADERBOARD,
            ])
        ;

        if ('worst' == $subop)
        {
            $me->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.dragonkills > 0 AND c.bestdragonage >= ?0');
        }

        $myRank = $me->getQuery()->getSingleScalarResult();

        $params['subTitle'] = [
            'section.days.subtitle',
            [
                'adverb' => $subop,
            ],
        ];

        $params['query']  = $query;
        $params['myRank'] = $myRank;

        return $this->renderHof($params);
    }

    protected function hofResurrects(array $params): Response
    {
        global $session;

        $order = $params['order'];
        $subop = $params['subop'];
        $query = $this->getQuery();

        $params['tpl'] = 'resurrects';

        $query->select('c.name', 'c.level')
            ->orderBy('c.resurrections', $order)
            ->addOrderBy('c.level', $order)
            ->addOrderBy('c.experience', $order)
        ;

        $me = clone $query;
        $me->select('count(1) AS count')
            ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.resurrections >= ?0')
            ->setParameters([
                0        => $session['user']['resurrections'],
                'permit' => SU_HIDE_FROM_LEADERBOARD,
            ])
        ;

        if ('worst' == $subop)
        {
            $me->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.resurrections <= ?0');
        }

        $myRank = $me->getQuery()->getSingleScalarResult();

        $params['subTitle'] = [
            'section.gems.subtitle',
            [
                'adverb' => $subop,
            ],
        ];

        $params['query']  = $query;
        $params['myRank'] = $myRank;

        return $this->renderHof($params);
    }

    protected function hofTough(array $params): Response
    {
        global $session;

        $order = $params['order'];
        $subop = $params['subop'];
        $query = $this->getQuery();

        $params['tpl'] = 'tough';

        $query->select('c.name', 'c.sex', 'c.race')
            ->orderBy('c.maxhitpoints', $order)
            ->addOrderBy('c.level', $order)
            ->addOrderBy('c.experience', $order)
        ;

        $me = clone $query;
        $me->select('count(1) AS count')
            ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.maxhitpoints >= ?0')
            ->setParameters([
                0        => $session['user']['maxhitpoints'],
                'permit' => SU_HIDE_FROM_LEADERBOARD,
            ])
        ;

        if ('worst' == $subop)
        {
            $me->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.maxhitpoints <= ?0');
        }

        $myRank = $me->getQuery()->getSingleScalarResult();

        $params['subTitle'] = [
            'section.tough.subtitle',
            [
                'adverb' => $subop,
            ],
        ];

        $params['query']  = $query;
        $params['myRank'] = $myRank;

        return $this->renderHof($params);
    }

    protected function hofCharm(array $params): Response
    {
        global $session;

        $order = $params['order'];
        $subop = $params['subop'];
        $query = $this->getQuery();

        $params['tpl'] = 'charm';

        $query->select('c.name', 'c.sex', 'c.race')
            ->orderBy('c.charm', $order)
            ->addOrderBy('c.level', $order)
            ->addOrderBy('c.experience', $order)
        ;

        $me = clone $query;
        $me->select('count(1) AS count')
            ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.charm >= ?0')
            ->setParameters([
                0        => $session['user']['charm'],
                'permit' => SU_HIDE_FROM_LEADERBOARD,
            ])
        ;

        if ('worst' == $subop)
        {
            $me->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.charm <= ?0');
        }

        $myRank = $me->getQuery()->getSingleScalarResult();

        $params['subTitle'] = [
            'section.charm.subtitle',
            [
                'adverb' => $subop,
            ],
        ];

        $params['query']  = $query;
        $params['myRank'] = $myRank;

        return $this->renderHof($params);
    }

    protected function hofGems(array $params): Response
    {
        global $session;

        $order = $params['order'];
        $subop = $params['subop'];
        $query = $this->getQuery();

        $params['tpl'] = 'gems';

        $query->select('c.name')
            ->orderBy('c.gems', $order)
            ->addOrderBy('c.level', $order)
            ->addOrderBy('c.experience', $order)
        ;

        $me = clone $query;
        $me->select('count(1) AS count')
            ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.gems >= ?0')
            ->setParameters([
                0        => $session['user']['gems'],
                'permit' => SU_HIDE_FROM_LEADERBOARD,
            ])
        ;

        if ('worst' == $subop)
        {
            $me->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.gems <= ?0');
        }

        $myRank = $me->getQuery()->getSingleScalarResult();

        $params['subTitle'] = [
            'section.gems.subtitle',
            [
                'adverb' => $subop,
            ],
        ];

        $params['query']  = $query;
        $params['myRank'] = $myRank;

        return $this->renderHof($params);
    }

    protected function hofMoney(array $params): Response
    {
        global $session;

        $order = $params['order'];
        $subop = $params['subop'];
        $query = $this->getQuery();

        $params['tpl'] = 'money';

        $query->select('c.name', 'round((0.95 * (c.gold + c.goldinbank)), 2) AS gold')
            ->orderBy('gold', $order)
            ->addOrderBy('c.level', $order)
            ->addOrderBy('c.experience', $order)
        ;

        $me = clone $query;
        $me->select('count(1) AS count')
            ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND round((0.95 * (c.gold + c.goldinbank)), 2) >= ?0')
            ->orderBy('round((0.95 * (c.gold + c.goldinbank)), 2)', $order)
            ->setParameters([
                0        => ($session['user']['gold'] + $session['user']['goldinbank']),
                'permit' => SU_HIDE_FROM_LEADERBOARD,
            ])
            ->setMaxResults(1)
        ;

        if ('worst' == $subop)
        {
            $me->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND gold <= ?0');
        }

        $myRank = $me->getQuery()->getSingleScalarResult();

        $params['subTitle'] = [
            'section.gems.subtitle',
            [
                'adverb' => $subop,
            ],
        ];

        $params['query']  = $query;
        $params['myRank'] = $myRank;

        return $this->renderHof($params);
    }

    protected function hofKills(array $params): Response
    {
        global $session;

        $params['tpl'] = 'default';
        $order         = $params['order'];
        $subop         = $params['subop'];
        $query         = $this->getQuery();

        $query->select('c.name', 'c.level', 'c.dragonkills', 'c.dragonage', 'c.bestdragonage')
            ->andWhere('c.dragonkills > 0')
            ->orderBy('c.dragonkills', $order)
            ->addOrderBy('c.level', $order)
            ->addOrderBy('c.experience', $order)
        ;

        $myRank = 0;
        if ($session['user']['dragonkills'] > 0)
        {
            $me = clone $query;

            $me->select('count(1) AS count')
                ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.dragonkills >= :kills')
                ->setParameters([
                    'kills'  => $session['user']['dragonkills'],
                    'permit' => SU_HIDE_FROM_LEADERBOARD,
                ])
                ->setMaxResults(1)
            ;

            if ('worst' == $subop)
            {
                $me->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.dragonkills <= :kills');
            }

            $myRank = $me->getQuery()->getSingleScalarResult();
        }

        $params['subTitle'] = [
            'section.default.subtitle',
            [
                'adverb' => $subop,
            ],
        ];

        $params['query']  = $query;
        $params['myRank'] = $myRank;

        return $this->renderHof($params);
    }

    private function renderHof(array $params): Response
    {
        /** @var Lotgd\Core\Repository\UserRepository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:User');

        $params['paginator'] = $repository->getPaginator($params['query'], $params['page'], 25);
        unset($params['query'], $params['page']);
        $params['footerTitle'] = [
            'section.footertitle',
            [
                'percent' => \round($params['myRank'] / $params['paginator']->getTotalItemCount(), 2),
            ],
        ];

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_HOF_POST);
        $params = modulehook('page-hof-tpl-params', $params);

        return $this->render('page/hof.html.twig', $params);
    }

    private function getQuery()
    {
        /** @var Lotgd\Core\Repository\UserRepository */
        $repository = $this->getDoctrine()->getRepository('LotgdCore:User');
        $query      = $repository->createQueryBuilder('u');

        $query
            ->leftJoin('LotgdCore:Avatar', 'c', 'WITH', $query->expr()->eq('c.acct', 'u.acctid'))
            ->where('u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0')
            ->setParameter('permit', SU_HIDE_FROM_LEADERBOARD)
        ;

        return $query;
    }
}
