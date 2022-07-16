<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core\Controller;

use Doctrine\Common\Collections\Criteria;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ListController extends AbstractController
{
    public const ITEM_PER_PAGE = 50;

    private $dispatcher;
    /** @var \Lotgd\Core\Repository\UserRepository */
    private $repository;
    private $navigation;
    private $response;

    public function __construct(EventDispatcherInterface $eventDispatcher, Navigation $navigation, HttpResponse $response)
    {
        $this->dispatcher = $eventDispatcher;
        $this->navigation = $navigation;
        $this->response   = $response;
    }

    public function index(Request $request): Response
    {
        global $session;

        //-- Init page
        $this->response->pageTitle('title', [], 'page_list');

        if ($session['user']['loggedin'])
        {
            if ($session['user']['alive'])
            {
                $this->navigation->villageNav();
            }
            else
            {
                $this->navigation->addNav('list.nav.graveyard', 'graveyard.php');
            }

            $this->navigation->addNav('list.nav.online', 'list.php');
            $this->navigation->addNav('list.nav.full', 'list.php?page=1');

            if ($session['user']['clanid'] > 0)
            {
                $this->navigation->addNav('Online Clan Members', 'list.php?op=clan');

                if ($session['user']['alive'])
                {
                    $this->navigation->addNav('Clan Hall', 'clan.php');
                }
            }
        }
        else
        {
            $this->navigation->addHeader('common.category.login');
            $this->navigation->addNav('common.nav.login', 'home.php');
            $this->navigation->addNav('list.nav.online', 'list.php');
            $this->navigation->addNav('list.nav.full', 'list.php?page=1');
        }

        $op   = $request->query->get('op');
        $page = $request->query->getInt('page');
        $method = method_exists($this, $op) ? $op : 'page';

        if ( ! $page && '' == $op)
        {
            $method = 'enter';
        }

        return $this->{$method}([], $request);
    }

    protected function enter(array $params, Request $request): Response
    {
        $search = $request->request->getAlnum('name', '');

        $query = $this->queryList($search);
        $query->andWhere('u.loggedin = 1');

        $result = $this->getRepository()->getPaginator($query, 1, self::ITEM_PER_PAGE);

        $params['title'] = ['title' => 'list.warriors.online', 'params' => ['n' => $result->getTotalItemCount()]];
        $params['result'] = $result;

        $this->navigation->pagination($result, 'list.php');

        return $this->renderList($params);
    }

    protected function clan(array $params, Request $request): Response
    {
        global $session;

        $page   = $request->query->getInt('page');
        $search = $request->request->getAlnum('name', '');

        $query = $this->queryList($search);
        $query->andWhere('u.loggedin = 1 AND c.clanid = :clan')
            ->setParameter('clan', $session['user']['clanid'])
        ;

        $result = $this->getRepository()->getPaginator($query, $page, self::ITEM_PER_PAGE);
        $this->navigation->pagination($result, 'list.php');

        $params['title'] = ['title' => 'list.clan.online', 'params' => ['n' => $result->getTotalItemCount()]];

        return $this->renderList($params);
    }

    protected function page(array $params, Request $request): Response
    {
        $page   = $request->query->getInt('page');
        $search = $request->request->getAlnum('name', '');

        $query  = $this->queryList($search);
        $result = $this->getRepository()->getPaginator($query, $page, self::ITEM_PER_PAGE);

        $params['title'] = ['title' => 'list.warriors.singlepage'];
        $params['result'] = $result;

        if ( ! empty($search))
        {
            $params['title'] = ['title' => 'list.warriors.search', 'params' => [
                'n'      => $result->getTotalItemCount(),
                'search' => $search,
            ]];
        }
        elseif ($result->count() >= 1)
        {
            $rangeMax = $result->getItemCountPerPage() * $result->count();
            $rangeMax = ($result->getTotalItemCount() >= $rangeMax ? $rangeMax : $result->getTotalItemCount());

            $params['title'] = ['title' => 'list.warriors.multipage', 'params' => [
                'page'       => $result->count(),
                'rangeMin'   => (($result->count() - 1) * $result->getItemCountPerPage()) + 1,
                'rangeMax'   => $rangeMax,
                'totalCount' => $result->getTotalItemCount(),
            ]];
        }

        return $this->renderList($params);
    }

    private function queryList(?string $search = null)
    {
        $query = $this->getRepository()->createQueryBuilder('u');

        $query
            ->select('u.acctid', 'u.login', 'u.laston', 'u.loggedin', 'u.lastip', 'u.uniqueid')
            ->addSelect('c.name', 'c.hitpoints', 'c.alive', 'c.location', 'c.race', 'c.sex', 'c.level')
            ->where('u.locked = 0')
            ->leftJoin('LotgdCore:Avatar', 'c', 'with', $query->expr()->eq('c.id', 'u.avatar'))
            ->orderBy('c.level', Criteria::DESC)
            ->addOrderBy('c.dragonkills', Criteria::DESC)
            ->addOrderBy('u.login', Criteria::ASC)
        ;

        if ( ! empty($search))
        {
            $query->andWhere('c.name LIKE :name')
                ->setParameter('name', "%{$search}%")
            ;
        }

        return $query;
    }

    private function getRepository(): UserRepository
    {
        if ( ! $this->repository instanceof UserRepository)
        {
            $this->repository = $this->getDoctrine()->getRepository('LotgdCore:User');
        }

        return $this->repository;
    }

    private function renderList(array $params): Response
    {
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_LIST_POST);
        $params = $args->getArguments();

        return $this->render('page/list.html.twig', $params);
    }
}
