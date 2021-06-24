<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

if ($session['user']['loggedin'] ?? false)
{
    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
}

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

//-- Init page
\LotgdResponse::pageStart('title', [], 'page_news');

$args = new GenericEvent(null, ['showLastMotd' => true]);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_NEWS_INTERCEPT);
$hookIntercept = modulehook('news-intercept', $args->getArguments());

$day = $request->query->getInt('day');
$timestamp = strtotime("-{$day} days");
$params = [
    'timestamp' => $timestamp,
    'date' => $timestamp,
];

if ($hookIntercept['showLastMotd'] ?? false)
{
    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Motd::class);
    $params['lastMotd'] = $repository->getLastMotd($session['user']['acctid'] ?? null);
}

if (! $session['user']['loggedin'])
{
    \LotgdNavigation::addHeader('common.category.login');
    \LotgdNavigation::addNav('common.nav.login', 'index.php');
}
elseif ($session['user']['alive'])
{
    \LotgdNavigation::villageNav();
}
else
{
    require_once 'lib/battle/extended.php';

    suspend_companions('allowinshades', true);

    \LotgdNavigation::addHeader('news.category.logout');
    \LotgdNavigation::addNav('news.nav.logout', 'login.php?op=logout');

    \LotgdNavigation::addHeader('news.category.dead', [
        'params' => [
            'sex' => (int) $session['user']['sex']
        ]
    ]);
    \LotgdNavigation::addNav('news.nav.shades', 'shades.php');
    \LotgdNavigation::addNav('news.nav.graveyard', 'graveyard.php');
}

\LotgdNavigation::addHeader('news.category.news');
\LotgdNavigation::addNav('news.nav.previous', 'news.php?day='.($day + 1));

if ($day > 0)
{
    \LotgdNavigation::addNav('news.nav.next', 'news.php?day='.($day - 1));
}

if ($session['user']['loggedin'])
{
    \LotgdNavigation::addNav('common.nav.preferences', 'prefs.php');
}
\LotgdNavigation::addNav('news.nav.about', 'about.php');

//-- Superuser menu
\LotgdNavigation::superuser();

$params['SU_EDIT_COMMENTS'] = $session['user']['superuser'] & SU_EDIT_COMMENTS;

$request->attributes->set('params', $params);

\LotgdResponse::callController(Lotgd\Core\Controller\NewsController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
