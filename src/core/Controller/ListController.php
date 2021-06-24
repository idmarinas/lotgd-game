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

use Lotgd\Core\Repository\AccountsRepository;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ListController extends AbstractController
{
    public const ITEM_PER_PAGE = 50;

    private $dispatcher;
    /** @var \Lotgd\Core\Repository\AccountsRepository */
    private $repository;
    private $navigation;

    public function __construct(EventDispatcherInterface $eventDispatcher, Navigation $navigation)
    {
        $this->dispatcher = $eventDispatcher;
        $this->navigation = $navigation;
    }

    public function index(Request $request): Response
    {
        $search = (string) $request->request->getAlnum('name', '');
        $params = [];

        $query = $this->queryList($search);
        $query->andWhere('u.loggedin = 1');

        $result = $this->getRepository()->getPaginator($query, 1, self::ITEM_PER_PAGE);

        $params = [
            'title'  => ['title' => 'list.warriors.online', 'params' => ['n' => $result->getTotalItemCount()]],
            'result' => $result,
        ];

        $this->navigation->pagination($result, 'list.php');

        return $this->renderList($params);
    }

    public function clan(Request $request): Response
    {
        global $session;

        $page   = (int) $request->query->getInt('page');
        $search = (string) $request->request->getAlnum('name', '');

        $query = $this->queryList($search);
        $query->andWhere('u.loggedin = 1 AND c.clanid = :clan')
            ->setParameter('clan', $session['user']['clanid'])
        ;

        $result = $this->getRepository()->getPaginator($query, $page, self::ITEM_PER_PAGE);
        $this->navigation->pagination($result, 'list.php');

        $params = [
            'title' => ['title' => 'list.clan.online', 'params' => ['n' => $result->getTotalItemCount()]],
        ];

        return $this->renderList($params);
    }

    public function page(Request $request): Response
    {
        $page   = (int) $request->query->getInt('page');
        $search = (string) $request->request->getAlnum('name', '');

        $query  = $this->queryList($search);
        $result = $this->getRepository()->getPaginator($query, $page, self::ITEM_PER_PAGE);

        $params = [
            'title' => ['title' => 'list.warriors.singlepage'],
            'result' => $result,
        ];

        if ($search)
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
            ->leftJoin('LotgdCore:Characters', 'c', 'with', $query->expr()->eq('c.id', 'u.character'))
            ->orderBy('c.level', 'DESC')
            ->addOrderBy('c.dragonkills', 'DESC')
            ->addOrderBy('u.login', 'ASC')
        ;

        if ($search)
        {
            $query->andWhere('c.name LIKE :name')
                ->setParameter('name', "%{$search}%")
            ;
        }

        return $query;
    }

    private function getRepository(): AccountsRepository
    {
        if ( ! $this->repository instanceof AccountsRepository)
        {
            $this->repository = $this->getDoctrine()->getRepository('LotgdCore:Accounts');
        }

        return $this->repository;
    }

    private function renderList(array $params): Response
    {
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_LIST_POST);
        $params = modulehook('page-list-tpl-params', $args->getArguments());

        return $this->render('page/list.html.twig', $params);
    }
}
