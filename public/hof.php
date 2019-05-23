<?php

// translator ready
// addnews ready
// mail ready

// New Hall of Fame features by anpera
// http://www.anpera.net/forum/viewforum.php?f=27

require_once 'common.php';

tlschema('hof');

checkday();

// Don't hook on to this text for your standard modules please, use "hof" instead.
// This hook is specifically to allow modules that do other hofs to create ambience.
$result = modulehook('hof-text-domain', ['textDomain' => 'page-hof', 'textDomainNavigation' => 'navigation-hof']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

page_header('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

$op = (string) \LotgdHttp::getQuery('op');
$subop = (string) \LotgdHttp::getQuery('subop');
$page = (int) \LotgdHttp::getQuery('page');
$subop = $subop ?: 'best';
$op = $op ?: 'kills';
$order = ('worst' == $subop) ? 'ASC' : 'DESC';

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.navigation');
\LotgdNavigation::villageNav();

\LotgdNavigation::addHeader('category.ranking');
\LotgdNavigation::addNav('nav.dragonkill', "hof.php?op=kills&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.gold', "hof.php?op=money&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.gem', "hof.php?op=gems&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.charm', "hof.php?op=charm&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.tough', "hof.php?op=tough&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.resurrect', "hof.php?op=resurrects&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.dragonspeed', "hof.php?op=days&subop={$subop}&page=1");

\LotgdNavigation::addHeader('category.sort');
\LotgdNavigation::addNav('nav.best', "hof.php?op={$op}&subop=best&page={$page}");
\LotgdNavigation::addNav('nav.worst', "hof.php?op={$op}&subop=worst&page={$page}");

\LotgdNavigation::addHeader('category.other');

modulehook('hof-add', []);

$repository = \Doctrine::getRepository('LotgdCore:Accounts');
$query = $repository->createQueryBuilder('u');

$query
    ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $query->expr()->eq('c.acct', 'u.acctid'))
    ->where('u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0')
    ->setParameter('permit', SU_HIDE_FROM_LEADERBOARD)
;

if ('money' == $op)
{
    $params['tpl'] = 'money';

    $query->select('c.name', 'round((0.95 * (c.gold + c.goldinbank)), 2) AS gold')
        ->orderBy('gold', $order)
        ->addOrderBy('c.level', $order)
        ->addOrderBy('c.experience', $order)
    ;

    $me = clone $query;
    $me->select('count(1) AS count')
        ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND round((0.95 * (c.gold + c.goldinbank)), 2) >= ?0')
        ->orderBy('round((0.95 * (c.gold + c.goldinbank)), 2)', $order)
        ->setParameters([
            0 => ($session['user']['gold'] + $session['user']['goldinbank']),
            'permit' => SU_HIDE_FROM_LEADERBOARD
        ])
        ->setMaxResults(1)
    ;

    if ('worst' == $subop)
    {
        $em->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND gold <= ?0');
    }

    $myRank = $me->getQuery()->getSingleScalarResult();

    $params['subTitle'] = [
        'section.gems.subtitle',
        [
            'adverb' => $subop
        ]
    ];
}
elseif ('gems' == $op)
{
    $params['tpl'] = 'gems';

    $query->select('c.name')
        ->orderBy('c.gems', $order)
        ->addOrderBy('c.level', $order)
        ->addOrderBy('c.experience', $order)
    ;

    $me = clone $query;
    $me->select('count(1) AS count')
        ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.gems >= ?0')
        ->setParameters([
            0 => $session['user']['gems'],
            'permit' => SU_HIDE_FROM_LEADERBOARD
        ])
    ;

    if ('worst' == $subop)
    {
        $em->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.gems <= ?0');
    }

    $myRank = $me->getQuery()->getSingleScalarResult();

    $params['subTitle'] = [
        'section.gems.subtitle',
        [
            'adverb' => $subop
        ]
    ];
}
elseif ('charm' == $op)
{
    $params['tpl'] = 'charm';

    $query->select('c.name', 'c.sex', 'c.race')
        ->orderBy('c.charm', $order)
        ->addOrderBy('c.level', $order)
        ->addOrderBy('c.experience', $order)
    ;

    $me = clone $query;
    $me->select('count(1) AS count')
        ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.charm >= ?0')
        ->setParameters([
            0 => $session['user']['charm'],
            'permit' => SU_HIDE_FROM_LEADERBOARD
        ])
    ;

    if ('worst' == $subop)
    {
        $em->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.charm <= ?0');
    }

    $myRank = $me->getQuery()->getSingleScalarResult();

    $params['subTitle'] = [
        'section.charm.subtitle',
        [
            'adverb' => $subop
        ]
    ];
}
elseif ('tough' == $op)
{
    $params['tpl'] = 'tough';

    $query->select('c.name', 'c.sex', 'c.race')
        ->orderBy('c.maxhitpoints', $order)
        ->addOrderBy('c.level', $order)
        ->addOrderBy('c.experience', $order)
    ;

    $me = clone $query;
    $me->select('count(1) AS count')
        ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.maxhitpoints >= ?0')
        ->setParameters([
            0 => $session['user']['maxhitpoints'],
            'permit' => SU_HIDE_FROM_LEADERBOARD
        ])
    ;

    if ('worst' == $subop)
    {
        $em->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.maxhitpoints <= ?0');
    }

    $myRank = $me->getQuery()->getSingleScalarResult();

    $params['subTitle'] = [
        'section.tough.subtitle',
        [
            'adverb' => $subop
        ]
    ];
}
elseif ('resurrects' == $op)
{
    $params['tpl'] = 'resurrects';

    $query->select('c.name', 'c.level')
        ->orderBy('c.resurrections', $order)
        ->addOrderBy('c.level', $order)
        ->addOrderBy('c.experience', $order)
    ;

    $me = clone $query;
    $me->select('count(1) AS count')
        ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.resurrections >= ?0')
        ->setParameters([
            0 => $session['user']['resurrections'],
            'permit' => SU_HIDE_FROM_LEADERBOARD
        ])
    ;

    if ('worst' == $subop)
    {
        $em->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.resurrections <= ?0');
    }

    $myRank = $me->getQuery()->getSingleScalarResult();

    $params['subTitle'] = [
        'section.gems.subtitle',
        [
            'adverb' => $subop
        ]
    ];
}
elseif ('days' == $op)
{
    $params['tpl'] = 'days';

    $order = ('worst' == $subop) ? 'DESC' : 'ASC';

    $query->select('c.name', 'c.bestdragonage')
        ->andWhere('c.dragonkills > 0')
        ->andWhere('c.bestdragonage > 0')
        ->orderBy('c.bestdragonage', $order)
        ->addOrderBy('c.level', $order)
        ->addOrderBy('c.experience', $order)
    ;

    $me = clone $query;
    $me->select('count(1) AS count')
        ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.dragonkills > 0 AND c.bestdragonage <= ?0')
        ->setParameters([
            0 => $session['user']['bestdragonage'],
            'permit' => SU_HIDE_FROM_LEADERBOARD
        ])
    ;

    if ('worst' == $subop)
    {
        $em->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.dragonkills > 0 AND c.bestdragonage >= ?0');
    }

    $myRank = $me->getQuery()->getSingleScalarResult();

    $params['subTitle'] = [
        'section.days.subtitle',
        [
            'adverb' => $subop
        ]
    ];
}
//-- Default is kills
else
{
    $params['tpl'] = 'default';

    $query->select('c.name', 'c.level', 'c.dragonkills', 'c.dragonage', 'c.bestdragonage')
        ->andWhere('c.dragonkills > 0')
        ->orderBy('c.dragonkills', $order)
        ->addOrderBy('c.level', $order)
        ->addOrderBy('c.experience', $order)
    ;

    $myRank = 0;
    if ($session['user']['dragonkills'] > 0)
    {
        $me = clone $query;

        $me->select('count(1) AS count')
            ->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.dragonkills >= :kills')
            ->setParameters([
                'kills' => $session['user']['dragonkills'],
                'permit' => SU_HIDE_FROM_LEADERBOARD
            ])
            ->setMaxResults(1)
        ;

        if ('worst' == $subop)
        {
            $em->where('(u.locked = 0 AND BIT_AND(u.superuser, :permit) = 0) AND c.dragonkills <= :kills');
        }

        $myRank = $me->getQuery()->getSingleScalarResult();
    }

    $params['subTitle'] = [
        'section.default.subtitle',
        [
            'adverb' => $subop
        ]
    ];
}

$params['paginator'] = $repository->getPaginator($query, $page, 25);

$params['footerTitle'] = [
    'section.footertitle',
    [
        'percent' => round($myRank / $params['paginator']->getTotalItemCount(), 2)
    ]
];

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-hof-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/hof.twig', $params));

page_footer();
