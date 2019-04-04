<?php

// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/villagenav.php';

if ($session['user']['loggedin'] ?? false)
{
    checkday();
}

tlschema('news');

page_header('title', [], 'page-news');

$hookIntercept = modulehook('news-intercept', ['showLastMotd' => true]);

$newsperpage = 50;
$page = (int) \LotgdHttp::getQuery('page');
$day = (int) \LotgdHttp::getQuery('day');
$op = (string) \LotgdHttp::getQuery('op');
$timestamp = strtotime("-{$day} days");
$params = ['date' => $timestamp];

if ($hookIntercept['showLastMotd'] ?? false)
{
    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Motd::class);
    $params['lastMotd'] = $repository->getLastMotd($session['user']['acctid'] ?? null);
}

$newsRepo = \Doctrine::getRepository(\Lotgd\Core\Entity\News::class);

if ('delete' == $op)
{
    checkSuPermission(SU_EDIT_COMMENTS, 'news.php');

    $newsId = (int) \LotgdHttp::getQuery('newsid');
    $newsRepo->deleteNewsId($newsId);
}

//-- Select
$select = \DB::select('news');
$select->order('newsid DESC')
    ->where->equalTo('newsdate', date('Y-m-d', $timestamp))
;

$result = \DB::paginator($select, $page, $newsperpage);
$params['result'] = $result;

if (! $session['user']['loggedin'])
{
    \LotgdNavigation::addHeader('common.category.login');
    \LotgdNavigation::addNav('common.nav.login', 'index.php');
}
elseif ($session['user']['alive'])
{
    villagenav();
}
else
{
    require_once 'lib/battle/extended.php';

    suspend_companions('allowinshades', true);

    \LotgdNavigation::addHeader('news.category.logout');
    \LotgdNavigation::addNav('news.nav.logout', 'login.php?op=logout');

    \LotgdNavigation::addHeader('news.category.dead', ['sex' => (int) $session['user']['sex']]);
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

\DB::pagination($params['result'], 'news.php');

$params['SU_EDIT_COMMENTS'] = $session['user']['superuser'] & SU_EDIT_COMMENTS;

$params = modulehook('page-news-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/news.twig', $params));

page_footer();
