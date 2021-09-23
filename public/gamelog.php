<?php

// translator ready
// addnews ready
// mail ready

// Written by Christian Rutsch

require_once 'common.php';

check_su_access(SU_EDIT_CONFIG);

$textDomain = 'grotto_gamelog';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

\LotgdNavigation::addHeader('common.nav.navigation');
\LotgdNavigation::superuserGrottoNav();

$category = (string) \LotgdRequest::getQuery('cat');
$sortorder = (int) \LotgdRequest::getQuery('sortorder'); // 0 = DESC 1= ASC
$sortby = (string) \LotgdRequest::getQuery('sortby');
$asc_desc = (0 == $sortorder ? 'DESC' : 'ASC');
$page = (int) \LotgdRequest::getQuery('page');

$params = [];
$repository = \Doctrine::getRepository('LotgdCore:Gamelog');
$query = $repository->createQueryBuilder('u');

$query
    ->select('u.logid', 'u.message', 'u.category', 'u.filed', 'u.date', 'u.who')
    ->addSelect('c.name')
    ->leftJoin(
        'LotgdCore:Avatar',
        'c',
        \Doctrine\ORM\Query\Expr\Join::WITH,
        $query->expr()->eq('u.who', 'c.acct')
    )
;

if ($category !== '' && $category !== '0')
{
    $query->where('u.category = :cat')
        ->setParameter('cat', $category)
    ;
}

if ($sortby !== '' && $sortby !== '0')
{
    $query->orderBy("u.{$sortby}", $asc_desc);
}

\LotgdNavigation::addHeader('gamelog.category.operations');
\LotgdNavigation::addNav('gamelog.nav.refresh', "gamelog.php?page={$page}&cat={$category}&sortorder={$sortorder}&sortby={$sortby}");
($category) && \LotgdNavigation::addNav('gamelog.nav.all', 'gamelog.php');

\LotgdNavigation::addHeader('gamelog.category.sorting');
\LotgdNavigation::addNav('gamelog.nav.sort.date.asc', "gamelog.php?page={$page}&cat={$category}&sortorder=1&sortby=date");
\LotgdNavigation::addNav('gamelog.nav.sort.date.desc', "gamelog.php?page={$page}&cat={$category}&sortorder=0&sortby=date");
\LotgdNavigation::addNav('gamelog.nav.sort.category.asc', "gamelog.php?page={$page}&cat={$category}&sortorder=1&sortby=category");
\LotgdNavigation::addNav('gamelog.nav.sort.category.desc', "gamelog.php?page={$page}&cat={$category}&sortorder=0&sortby=category");

$params['paginator'] = $repository->getPaginator($query, $page, 200);

$category = clone $query;
$categories = $category->groupBy('u.category')->getQuery()->getResult();

\LotgdNavigation::addHeader('gamelog.category.operations');

foreach ($categories as $value)
{
    \LotgdNavigation::addNav('gamelog.nav.view.by', "gamelog.php?cat={$value['category']}", [
        'params' => [
            'category' => $value['category']
        ]
    ]);
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/gamelog.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
