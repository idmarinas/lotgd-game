<?php

// translator ready
// addnews ready
// mail ready

// Written by Christian Rutsch

require_once 'common.php';

check_su_access(SU_EDIT_CONFIG);

$textDomain = 'grotto-gamelog';

page_header('title', [], $textDomain);

\LotgdNavigation::addHeader('common.nav.navigation');
\LotgdNavigation::superuserGrottoNav();

$category = (string) \LotgdHttp::getQuery('cat');
$sortorder = (int) \LotgdHttp::getQuery('sortorder'); // 0 = DESC 1= ASC
$sortby = (string) \LotgdHttp::getQuery('sortby');
$asc_desc = (0 == $sortorder ? 'DESC' : 'ASC');
$page = (int) \LotgdHttp::getQuery('page');

$params = [];
$repository = \Doctrine::getRepository('LotgdCore:Gamelog');
$query = $repository->createQueryBuilder('u');

$query
    ->select('u.logid', 'u.message', 'u.category', 'u.filed', 'u.date', 'u.who')
    ->addSelect('c.name')
    ->leftJoin(
        'LotgdCore:Characters',
        'c',
        \Doctrine\ORM\Query\Expr\Join::WITH,
        $query->expr()->eq('u.who', 'c.acct')
    )
;

if ($category)
{
    $query->where('u.category = :cat')
        ->setParameter('cat', $category)
    ;
}

if ($sortby)
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
rawoutput(LotgdTheme::renderLotgdTemplate('core/page/gamelog.twig', $params));

page_footer();
