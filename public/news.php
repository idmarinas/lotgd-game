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
$page = (int) httpget('page');
$day = (int) httpget('day');
$timestamp = strtotime("-{$day} days");
$params = ['date' => $timestamp];

if ($hookIntercept['showLastMotd'] ?? false)
{
    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Motd::class);
    $params['lastMotd'] = $repository->getLastMotd($session['user']['acctid'] ?? null);
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

    \LotgdNavigation::addHeader('Log Out');
    \LotgdNavigation::addNav('Log out', 'login.php?op=logout');

    \LotgdNavigation::addHeader('news.dead', ['sex' => (int) $session['user']['sex']]);
    \LotgdNavigation::addNav('S?Land of Shades', 'shades.php');
    \LotgdNavigation::addNav('G?The Graveyard', 'graveyard.php');
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
LotgdNavigation::addNav('news.nav.about', 'about.php');

\LotgdNavigation::addHeader('common.category.superuser');
if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
{
    \LotgdNavigation::addNav('common.nav.moderation', 'moderate.php');
}

if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO)
{
    \LotgdNavigation::addNav('common.nav.grotto', 'superuser.php');
}

if ($session['user']['superuser'] & SU_INFINITE_DAYS)
{
    \LotgdNavigation::addNav('common.nav.newday', 'newday.php');
}

DB::pagination($params['result'], 'news.php');

$params['SU_EDIT_COMMENTS'] = $session['user']['superuser'] & SU_EDIT_COMMENTS;
$params['REQUEST_URI'] = \LotgdHttp::getServer('REQUEST_URI');

$params = modulehook('page-news-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('pages/news.twig', $params));

page_footer();
