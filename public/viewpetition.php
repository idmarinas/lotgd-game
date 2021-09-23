<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

check_su_access(SU_EDIT_PETITIONS);

$statuses = [
    5 => 'statuses.05',
    4 => 'statuses.04',
    0 => 'statuses.00',
    1 => 'statuses.01',
    6 => 'statuses.06',
    7 => 'statuses.07',
    3 => 'statuses.03',
    2 => 'statuses.02',
];

$args = new GenericEvent(null, $statuses);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_PETITION_STATUS);
$statuses = modulehook('petition-status', $args->getArguments());
reset($statuses);

$op = (string) \LotgdRequest::getQuery('op');
$petitionId = (int) \LotgdRequest::getQuery('id');
$page = (int) \LotgdRequest::getQuery('page');

$textDomain = 'grotto_viewpetition';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'statuses' => $statuses
];

$repository = \Doctrine::getRepository('LotgdCore:Petitions');

\LotgdNavigation::superuserGrottoNav();

if ('' == $op)
{
    $params['tpl'] = 'default';

    $setstat = (int) \LotgdRequest::getQuery('setstat');

    if ('' != $setstat)
    {
        $result = $repository->find($petitionId);

        if ($result && $result->getStatus() != $setstat)
        {
            $result->setStatus($setstat)
                ->setCloseuserid($session['user']['acctid'])
                ->setClosedate(new \DateTime('now'))
            ;
            \Doctrine::persist($result);
            \Doctrine::flush();
        }
    }

    $query = $repository->createQueryBuilder('u');

    $query->select('u.petitionid', 'u.date', 'u.status', 'u.body', 'u.closedate')
        ->addSelect('c.name AS authorName')
        ->addSelect('c1.name AS closerName')
        ->addSelect("(SELECT count(o.id) FROM LotgdCore:Commentary o WHERE o.section = concat('pet-', u.petitionid)) AS comments")
        ->leftJoin('LotgdCore:Avatar', 'c', 'WITH', $query->expr()->eq('c.acct', 'u.author'))
        ->leftJoin('LotgdCore:Avatar', 'c1', 'WITH', $query->expr()->eq('c1.acct', 'u.closeuserid'))
        ->orderBy('u.status', 'ASC')
        ->addOrderBy('u.date', 'ASC')
    ;

    $params['paginator'] = $repository->getPaginator($query, $page, 50);

    \LotgdNavigation::addHeader('viewpetitions.category.petitions');
    \LotgdNavigation::addNav('viewpetitions.nav.refresh', 'viewpetition.php');
}
elseif ('view' == $op)
{
    $params['tpl'] = 'view';
    $viewpageinfo = (int) \LotgdRequest::getQuery('viewpageinfo');

    \LotgdNavigation::addHeader('viewpetitions.category.petitions');
    \LotgdNavigation::addHeader('viewpetitions.category.details');

    if ($viewpageinfo !== 0)
    {
        \LotgdNavigation::addNav('viewpetitions.nav.details.hide', "viewpetition.php?op=view&id={$petitionId}}");
    }
    else
    {
        \LotgdNavigation::addNav('viewpetitions.nav.details.show', "viewpetition.php?op=view&id={$petitionId}&viewpageinfo=1");
    }

    \LotgdNavigation::addHeader('common.category.navigation');
    \LotgdNavigation::addNav('viewpetitions.nav.viewer', 'viewpetition.php');

    \LotgdNavigation::addHeader('viewpetitions.category.ops.user');
    \LotgdNavigation::addHeader('viewpetitions.category.ops.petition');

    $params['viewPageInfo'] = $viewpageinfo;

    if (count($statuses) > 0)
    {
        reset($statuses);
        foreach($statuses as $key => $val)
        {
            \LotgdNavigation::addNav('viewpetitions.nav.mark', "viewpetition.php?setstat={$key}&id={$petitionId}", [
                'params' => [
                    'key' => substr(\LotgdSanitize::fullSanitize($val), 0, 1),
                    'petition' => \LotgdTranslator::t($val, [], $textDomain)
                ]
            ]);
        }
    }

    $query = $repository->createQueryBuilder('u');

    $params['petition'] = $query->select('u.author', 'u.date', 'u.closedate', 'u.status', 'u.petitionid', 'u.ip', 'u.body', 'u.pageinfo')
        ->addSelect('c1.name AS closerName')
        ->addSelect('c.name AS authorName')
        ->addSelect('a.login', 'a.acctid')
        ->leftJoin('LotgdCore:Avatar', 'c', 'with', $query->expr()->eq('c.acct', 'u.author'))
        ->leftJoin('LotgdCore:Avatar', 'c1', 'with', $query->expr()->eq('c1.acct', 'u.closeuserid'))
        ->leftJoin('LotgdCore:User', 'a', 'with', $query->expr()->eq('a.acctid', 'u.author'))
        ->where('u.petitionid = :petition')
        ->setParameter('petition', $petitionId)

        ->getQuery()
        ->getSingleResult()
    ;

    \LotgdNavigation::addHeader('viewpetitions.category.ops.user');
    if ($params['petition']['acctid'])
    {
        \LotgdNavigation::addNav('viewpetitions.nav.user.bio', "bio.php?char={$params['petition']['acctid']}&ret=%2Fviewpetition.php%3Fop%3Dview%26id=$petitionId");
    }

    if ($params['petition']['acctid'] > 0 && $session['user']['superuser'] & SU_EDIT_USERS)
    {
        \LotgdNavigation::addNav('viewpetitions.nav.user.edit', "user.php?op=edit&userid={$params['petition']['acctid']}&returnpetition=$petitionId");
    }

    if ($params['petition']['acctid'] > 0 && $session['user']['superuser'] & SU_EDIT_DONATIONS)
    {
        \LotgdNavigation::addNav('viewpetitions.nav.user.donation', 'donators.php?op=add&name='.rawurlencode($params['petition']['login']).'&ret='.urlencode($_SERVER['REQUEST_URI']));
    }
}

\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/viewpetition.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();

