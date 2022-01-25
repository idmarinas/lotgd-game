<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

check_su_access(SU_EDIT_CONFIG);

$textDomain = 'grotto_stats';

$params = [
    'textDomain' => $textDomain,
];

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addNav('stats.nav.refresh', 'stats.php');

LotgdNavigation::addHeader('stats.category.types');
LotgdNavigation::addNav('stats.nav.stats', 'stats.php?op=stats');
LotgdNavigation::addNav('stats.nav.referers', 'stats.php?op=referers');
LotgdNavigation::addNav('stats.nav.graph', 'stats.php?op=graph');

$repository     = Doctrine::getRepository('LotgdCore:AccountsEverypage');
$acctRepository = Doctrine::getRepository('LotgdCore:User');

$op = (string) LotgdRequest::getQuery('op');

if ('stats' == $op || '' == $op)
{
    $params['tpl'] = 'default';

    $query = $repository->createQueryBuilder('u');

    $params['stats'] = $query->select('sum(u.gentimecount) AS c', 'sum(u.gentime) as t', 'sum(u.gensize) AS s', 'count(u.acctid) AS a')
        ->getQuery()
        ->getSingleResult()
    ;
}
elseif ('referers' == $op)
{
    $params['tpl'] = 'referers';

    $query = $acctRepository->createQueryBuilder('u');

    $result = $query->select('u')
        ->addSelect('a')
        ->leftJoin('LotgdCore:User', 'a', 'WITH', $query->expr()->eq('a.acctid', 'u.referer'))
        ->where('u.referer > 0')
        ->getQuery()
        ->getResult()
    ;

    $params['paginator'] = [];
    $params['referers']  = [];

    foreach ($result as $row)
    {
        if ($row->getReferer())
        {
            $params['paginator'][$row->getReferer()][] = $row;
        }
        else
        {
            $params['referers'][$row->getAcctid()] = $row;
        }
    }
}
elseif ('graph' == $op)
{
    $params['tpl'] = 'graph';

    $query = $acctRepository->createQueryBuilder('u');

    $params['paginator'] = $query->select('count(u.acctid) AS c', 'u.laston', 'date(u.laston) AS glaston')
        ->groupBy('glaston')
        ->orderBy('u.laston', 'DESC')
        ->getQuery()
        ->getResult()
    ;
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/stats.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
