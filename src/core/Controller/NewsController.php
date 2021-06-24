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
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NewsController extends AbstractController
{
    public const NEWS_PER_PAGE = 50;

    private $navigation;
    private $dispatcher;

    public function __construct(Navigation $navigation, EventDispatcherInterface $eventDispatcher)
    {
        $this->navigation = $navigation;
        $this->dispatcher = $eventDispatcher;
    }

    public function index(array $params, Request $request): Response
    {
        /** @var Lotgd\Core\Repository\NewsRepository */
        $newsRepo = $this->getDoctrine()->getRepository('LotgdCore:News');
        $page     = $request->query->getInt('page');
        $day      = $request->query->getInt('day');

        $query = $newsRepo->createQueryBuilder('u');
        $query->orderBy('u.id', 'DESC')
            ->where('u.date = :date')
            ->setParameter('date', \date('Y-m-d', $params['timestamp']))
        ;

        if ('delete' == $request->query->get('op'))
        {
            checkSuPermission(SU_EDIT_COMMENTS, 'news.php');

            $newsId = $request->query->getInt('newsid');
            $newsRepo->deleteNewsId($newsId);
        }

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_NEWS_POST);
        $params = modulehook('page-news-tpl-params', $args->getArguments());

        $params['result'] = $newsRepo->getPaginator($query, $page, self::NEWS_PER_PAGE);

        $this->navigation->pagination($params['result'], "news.php?day={$day}");

        return $this->render('page/news.html.twig', $params);
    }
}
