<?php

// addnews ready
// translator ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/villagenav.php';

page_header('title', [], 'page-list');

if ($session['user']['loggedin'])
{
    checkday();

    if ($session['user']['alive'])
    {
        villagenav();
    }
    else
    {
        LotgdNavigation::addNav('list.nav.graveyard', 'graveyard.php');
    }
    LotgdNavigation::addNav('list.nav.online', 'list.php');
    LotgdNavigation::addNav('list.nav.full', 'list.php?page=1');

    if ($session['user']['clanid'] > 0)
    {
        LotgdNavigation::addNav('Online Clan Members', 'list.php?op=clan');

        if ($session['user']['alive'])
        {
            LotgdNavigation::addNav('Clan Hall', 'clan.php');
        }
    }
}
else
{
    LotgdNavigation::addHeader('common.category.login');
    LotgdNavigation::addNav('common.nav.login', 'index.php');
    LotgdNavigation::addNav('list.nav.online', 'list.php');
    LotgdNavigation::addNav('list.nav.full', 'list.php?page=1');
}

$op = httpget('op');
$page = (int) httpget('page');
$search = (string) httppost('name');
$playersperpage = 50;
$params = [];

$select = DB::select(['a' => 'accounts']);
$select->columns(['acctid', 'login', 'laston', 'loggedin', 'lastip', 'uniqueid'])
    ->join(['c' => 'characters'], DB::expression('c.acct_id = a.acctid AND c.id = a.character_id'), ['name', 'hitpoints', 'alive', 'location', 'race', 'sex', 'level'])
    ->order('c.level DESC, c.dragonkills DESC, a.login ASC')
    ->where->equalTo('locked', 0)
;

if ('search' == $op)
{
    $select->where->like('name', "%$search%");
}

// Order the list by level, dragonkills, name so that the ordering is total!
// Without this, some users would show up on multiple pages and some users
// wouldn't show up
if (! $page && '' == $op)
{
    $select->where->equalTo('loggedin', 1)
        ->greaterThan('laston', date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds')))
    ;

    $result = DB::paginator($select, $page, $playersperpage);

    $params['title'] = ['title' => 'list.warriors.online', 'params' => ['n' => $result->getTotalItemCount()]];
}
elseif ('clan' == $op)
{
    $select->where->equalTo('loggedin', 1)
        ->greaterThan('laston', date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds')))
        ->equalTo('clanid', $session['user']['clanid'])
    ;
    $result = DB::paginator($select, $page, $playersperpage);

    $params['title'] = ['title' => 'list.clan.online', 'params' => ['n' => $result->getTotalItemCount()]];
}
else
{
    $result = DB::paginator($select, $page, $playersperpage);

    $params['title'] = ['title' => 'list.warriors.singlepage'];

    if ('search' == $op)
    {
        $params['title'] = ['title' => 'list.warriors.search', 'params' => [
            'n' => $result->getTotalItemCount(),
            'search' => $search
        ]];
    }
    elseif ($result->count() >= 1)
    {
        $rangeMax = $result->getItemCountPerPage() * $result->count();
        $rangeMax = ($result->getTotalItemCount() >= $rangeMax ? $rangeMax : $result->getTotalItemCount());

        $params['title'] = ['title' => 'list.warriors.multipage', 'params' => [
            'page' => $result->count(),
            'rangeMin' => (($result->count() - 1) * $result->getItemCountPerPage()) + 1,
            'rangeMax' => $rangeMax,
            'totalCount' => $result->getTotalItemCount()
        ]];
    }
}

$params['result'] = $result;
DB::pagination($result, 'list.php');

$params = modulehook('page-list-tpl-params', $params);
rawoutput(LotgdTheme::renderThemeTemplate('pages/list.twig', $params));

page_footer();
