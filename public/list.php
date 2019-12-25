<?php

// addnews ready
// translator ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

page_header('title', [], 'page-list');

if ($session['user']['loggedin'])
{
    checkday();

    if ($session['user']['alive'])
    {
        \LotgdNavigation::villageNav();
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

$op = \LotgdHttp::getQuery('op');
$page = (int) \LotgdHttp::getQuery('page');
$search = (string) \LotgdHttp::getPost('name');
$playersperpage = 50;
$params = [];

$repository = \Doctrine::getRepository('LotgdCore:Accounts');
$query = $repository->createQueryBuilder('u');

$query
    ->select('u.acctid', 'u.login', 'u.laston', 'u.loggedin', 'u.lastip', 'u.uniqueid')
    ->addSelect('c.name', 'c.hitpoints', 'c.alive', 'c.location', 'c.race', 'c.sex', 'c.level')
    ->where('u.locked = 0')
    ->leftJoin('LotgdCore:Characters', 'c', 'with', $query->expr()->eq('c.id', 'u.character'))
    ->orderBy('c.level', 'DESC')
    ->addOrderBy('c.dragonkills', 'DESC')
    ->addOrderBy('u.login', 'ASC')
;

if ('search' == $op)
{
    $query->andWhere('c.name LIKE :name')
        ->setParameter('name', "%{$search}%")
    ;
}

// Order the list by level, dragonkills, name so that the ordering is total!
// Without this, some users would show up on multiple pages and some users
// wouldn't show up
if (! $page && '' == $op)
{
    $query->andWhere('u.loggedin = 1');

    $result = $repository->getPaginator($query, $page, $playersperpage);

    $params['title'] = ['title' => 'list.warriors.online', 'params' => ['n' => $result->getTotalItemCount()]];
}
elseif ('clan' == $op)
{
    $query->andWhere('u.loggedin = 1 AND c.clanid = :clan')
        ->setParameter('clan', $session['user']['clanid'])
    ;

    $result = $repository->getPaginator($query, $page, $playersperpage);

    $params['title'] = ['title' => 'list.clan.online', 'params' => ['n' => $result->getTotalItemCount()]];
}
else
{
    $result = $repository->getPaginator($query, $page, $playersperpage);

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
rawoutput(LotgdTheme::renderThemeTemplate('page/list.twig', $params));

page_footer();
