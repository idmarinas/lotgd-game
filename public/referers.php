<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';

check_su_access(SU_EDIT_CONFIG);

$repository = \Doctrine::getRepository('LotgdCore:Referers');

$op = (string) \LotgdRequest::getQuery('op');
$sort = (string) \LotgdRequest::getQuery('sort');
$ascDescRaw = (int) \LotgdRequest::getQuery('direction');

$sort = $sort ?: 'count';
$ascDesc = $ascDescRaw !== 0 ? 'ASC' : 'DESC';

if ('rebuild' == $op)
{
    $result = $repository->findAll();

    foreach ($result as $row)
    {
        $site = str_replace('http://', '', $row->getUri());

        if (strpos($site, '/'))
        {
            $site = substr($site, 0, strpos($site, '/'));
        }

        $row->setSite($site);

        \Doctrine::persist($row);
    }
}
elseif('expire' == $op)
{
    $expire = (int) LotgdSetting::getSetting('expirecontent', 180);

    if ($expire > 0)
    {
        $repository->deleteExpireReferers($expire);
    }
}

\Doctrine::flush();

$textDomain = 'grotto_referers';

$params = [
    'textDomain' => $textDomain
];

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('referers.category.options');
\LotgdNavigation::addNav('referers.nav.refresh', 'referers.php?sort='.urlencode($sort).'&direction='.$ascDescRaw);
\LotgdNavigation::addNav('referers.nav.count', 'referers.php?sort=count&direction='.$ascDescRaw);
\LotgdNavigation::addNav('referers.nav.url', 'referers.php?sort=uri&direction='.$ascDescRaw);
\LotgdNavigation::addNav('referers.nav.time', 'referers.php?sort=last&direction='.$ascDescRaw);
\LotgdNavigation::addNav('referers.nav.switch', 'referers.php?sort='.urlencode($sort).'&direction='.(! $ascDescRaw));

\LotgdNavigation::addHeader('referers.category.optimization');
\LotgdNavigation::addNav('referers.nav.rebuild', 'referers.php?op=rebuild');
\LotgdNavigation::addNav('referers.nav.expire', 'referers.php?op=expire');

$query = $repository->createQueryBuilder('u');

$result = $query->select('u')
    ->addSelect('(SELECT sum(c.count) FROM LotgdCore:Referers c GROUP BY u.site) AS total', '(SELECT max(a.last) FROM LotgdCore:Referers a GROUP BY u.site) as recent')
    ->orderBy('u.site', 'ASC')
    ->addOrderBy("u.{$sort}", $ascDesc)
    ->setMaxResults(250)
    ->getQuery()
    ->getResult()
;

$params['paginator'] = [];

foreach($result as $row)
{
    $params['paginator'][$row[0]->getSite()]['data'] = [
        'total' => $row['total'],
        'recent' => $row['recent']
    ];
    $params['paginator'][$row[0]->getSite()]['rows'][] = $row[0];
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/referers.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
