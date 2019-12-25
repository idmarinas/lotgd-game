<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/dhms.php';

check_su_access(SU_EDIT_CONFIG);

$textDomain = 'page-debug';

page_header('title', [], $textDomain);

$page = (int) \LotgdHttp::getQuery('page');
$sort = (string) \LotgdHttp::getQuery('sort');
$debug = (string) \LotgdHttp::getQuery('debug');
$ascDescRaw = (int) \LotgdHttp::getQuery('direction');

$order = $sort ?: 'sum';
$ascDesc = $ascDescRaw ? 'ASC' : 'DESC';
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

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/debug.twig', $params));

page_footer();
