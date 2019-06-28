<?php

// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

tlschema('referral');

$textDomain = 'page-referral';

if (! $session['user']['loggedin'])
{
    $referral = (string) \LotgdHttp::getQuery('r');

    \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.referral.create', [ 'referral' => $referral ], $textDomain));

    return redirect('create.php?r='.rawurlencode($referral));
}

page_header('title', [], $textDomain);

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

$url = getsetting('serverurl', sprintf('%s://%s', \LotgdHttp::getServer('REQUEST_SCHEME'), \LotgdHttp::getServer('HTTP_HOST')));

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
rawoutput(\LotgdTheme::renderThemeTemplate('page/referral.twig', $params));

page_footer();
