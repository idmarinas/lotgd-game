<?php

// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

$textDomain = 'page-referral';

if (! $session['user']['loggedin'])
{
    $referral = (string) \LotgdRequest::getQuery('r');

    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.referral.create', [ 'referral' => $referral ], $textDomain));

    return redirect('create.php?r='.rawurlencode($referral));
}

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

if (file_exists('public/lodge.php'))
{
    \LotgdNavigation::addNav('common.nav.lodge', 'lodge.php');
}
else
{
    \LotgdNavigation::villageNav();
}

$url = getsetting('serverurl', sprintf('%s://%s', \LotgdRequest::getServer('REQUEST_SCHEME'), \LotgdRequest::getServer('HTTP_HOST')));

if (! preg_match('/\\/$/', $url))
{
    $url = $url.'/';
    savesetting('serverurl', $url);
}

$params['serverUrl'] = $url;
$params['refererAward'] = getsetting('refereraward', 25);
$params['referMinLevel'] = getsetting('referminlevel', 4);

$repository = \Doctrine::getRepository('LotgdCore:Accounts');
$query = $repository->createQueryBuilder('u');

$params['referrers'] = $query->select('u.refererawarded')
    ->addSelect('c.name', 'c.level', 'c.dragonkills')
    ->where('u.referer = :acct')
    ->leftJoin('LotgdCore:Characters', 'c', 'WITH', $query->expr()->eq('c.id', 'u.character'))
    ->setParameter('acct', $session['user']['acctid'])

    ->getQuery()
    ->getResult()
;

$params = modulehook('page-referral-tpl-params', $params);
\LotgdResponse::pageAddContent(\LotgdTheme::render('{theme}/pages/referral.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
