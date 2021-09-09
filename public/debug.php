<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

check_su_access(SU_EDIT_CONFIG);

$textDomain = 'grotto_debug';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$page = (int) \LotgdRequest::getQuery('page');
$sort = (string) \LotgdRequest::getQuery('sort');
$debug = (string) \LotgdRequest::getQuery('debug');
$ascDescRaw = (int) \LotgdRequest::getQuery('direction');

$order = $sort ?: 'sum';
$ascDesc = $ascDescRaw !== 0 ? 'ASC' : 'DESC';
$debug = $debug ?: 'pageruntime';

\LotgdNavigation::superuserGrottoNav();
\LotgdNavigation::addHeader('debug.category.option');
\LotgdNavigation::addNav('debug.nav.page', 'debug.php?debug=pageruntime&sort='.urlencode($sort));
\LotgdNavigation::addNav('debug.nav.module', 'debug.php?debug=hooksort&sort='.urlencode($sort));

\LotgdNavigation::addHeader('debug.category.sorting');
\LotgdNavigation::addNav('debug.nav.total', 'debug.php?debug='.$debug.'&sort=sum&direction='.$ascDescRaw);
\LotgdNavigation::addNav('debug.nav.avg', 'debug.php?debug='.$debug.'&sort=medium&direction='.$ascDescRaw);
\LotgdNavigation::addNav('debug.nav.switch', 'debug.php?debug='.$debug.'&sort='.urlencode($sort).'&direction='.(! $ascDescRaw));

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Debug::class);

$query = $repository->createQueryBuilder('u');

$query->select('u.category', 'u.subcategory', 'sum(u.value) AS sum', 'sum(u.value)/count(u.id) AS medium', 'COUNT(u.id) AS counter')
    ->orderBy($order, $ascDesc)
    ->groupBy('u.type', 'u.category', 'u.subcategory')
    ->where('u.type = :type')
    ->setParameter('type', 'pagegentime')
;

if ('hooksort' == $debug)
{
    $query->setParameter('type', 'hooktime');
}

$params['paginator'] = $repository->getPaginator($query, $page, 50);

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/debug.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
