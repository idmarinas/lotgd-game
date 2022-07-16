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

use Lotgd\Core\Combat\Battle;
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
    private $battle;

    public function __construct(
        Navigation $navigation,
        EventDispatcherInterface $eventDispatcher,
        Battle $battle
    ) {
        $this->navigation = $navigation;
        $this->dispatcher = $eventDispatcher;
        $this->battle     = $battle;
    }

    public function index(Request $request): Response
    {
        global $session;

        $args = new GenericEvent(null, ['showLastMotd' => true]);
        $this->dispatcher->dispatch($args, Events::PAGE_NEWS_INTERCEPT);
        $hookIntercept = $args->getArguments();

        $day       = $request->query->getInt('day');
        $timestamp = strtotime("-{$day} days");
        $params    = [
            'timestamp' => $timestamp,
            'date'      => $timestamp,
        ];

        if ($hookIntercept['showLastMotd'] ?? false)
        {
            /** @var \Lotgd\Core\Repository\MotdRepository $repository */
            $repository         = $this->getDoctrine()->getRepository('LotgdCore:Motd');
            $params['lastMotd'] = $repository->getLastMotd();
        }

        if ( ! $session['user']['loggedin'])
        {
            $this->navigation->addHeader('common.category.login');
            $this->navigation->addNav('common.nav.login', 'index.php');
        }
        elseif ($session['user']['alive'])
        {
            $this->navigation->villageNav();
        }
        else
        {
            $this->battle->suspendCompanions('allowinshades', true);

            $this->navigation->addHeader('news.category.logout');
            $this->navigation->addNav('news.nav.logout', 'login.php?op=logout');

            $this->navigation->addHeader('news.category.dead', [
                'params' => [
                    'sex' => (int) $session['user']['sex'],
                ],
            ]);
            $this->navigation->addNav('news.nav.shades', 'shades.php');
            $this->navigation->addNav('news.nav.graveyard', 'graveyard.php');
        }

        $this->navigation->addHeader('news.category.news');
        $this->navigation->addNav('news.nav.previous', 'news.php?day='.($day + 1));

        if ($day > 0)
        {
            $this->navigation->addNav('news.nav.next', 'news.php?day='.($day - 1));
        }

        if ($session['user']['loggedin'])
        {
            $this->navigation->addNav('common.nav.preferences', 'prefs.php');
        }
        $this->navigation->addNav('news.nav.about', 'about.php');

        //-- Superuser menu
        $this->navigation->superuser();

        $params['SU_EDIT_COMMENTS'] = $session['user']['superuser'] & SU_EDIT_COMMENTS;

        /** @var Lotgd\Core\Repository\NewsRepository $newsRepo */
        $newsRepo = $this->getDoctrine()->getRepository('LotgdCore:News');
        $page     = $request->query->getInt('page');
        $day      = $request->query->getInt('day');

        $query = $newsRepo->createQueryBuilder('u');
        $query->orderBy('u.id', 'DESC')
            ->where('u.date = :date')
            ->setParameter('date', date('Y-m-d', $params['timestamp']))
        ;

        if ('delete' == $request->query->get('op'))
        {
            checkSuPermission(SU_EDIT_COMMENTS, 'news.php');

            $newsId = $request->query->getInt('newsid');
            $newsRepo->deleteNewsId($newsId);
        }

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_NEWS_POST);
        $params = $args->getArguments();

        $params['result'] = $newsRepo->getPaginator($query, $page, self::NEWS_PER_PAGE);

        $this->navigation->pagination($params['result'], "news.php?day={$day}");

        return $this->render('page/news.html.twig', $params);
    }
}
