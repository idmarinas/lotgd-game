<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/nltoappon.php';
require_once 'lib/systemmail.php';

// Don't hook on to this text for your standard modules please, use "clan" instead.
// This hook is specifically to allow modules that do other clans to create ambience.
$result = modulehook('clan-text-domain', ['textDomain' => 'page-clan', 'textDomainNavigation' => 'navigation-clan']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$op = (string) \LotgdRequest::getQuery('op');

$costGold = (int) getsetting('goldtostartclan', 10000);
$costGems = (int) getsetting('gemstostartclan', 15);

$params = [
    'textDomain' => $textDomain,
    'clanInfo' => $claninfo,
    'clanOwnerName' => getsetting('clanregistrar', '`%Karissa`0'),
    'costGold' => $costGold,
    'costGems' => $costGems,
    'includeTemplatesPre' => [],
    'includeTemplatesPost' => [],
];

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.village');
\LotgdNavigation::villageNav();

\LotgdNavigation::addHeader('category.options');
\LotgdNavigation::addNav('nav.list.list', 'clan.php?op=list');

//-- Init page
\LotgdResponse::pageStart();

$ranks = [
    CLAN_APPLICANT => 'ranks.00',
    CLAN_MEMBER => 'ranks.010',
    CLAN_OFFICER => 'ranks.020',
    CLAN_ADMINISTRATIVE => 'ranks.025',
    CLAN_LEADER => 'ranks.030',
    CLAN_FOUNDER => 'ranks.031'
];

$ranks = ['ranks' => $ranks, 'textDomain' => 'page-clan', 'clanid' => null];
\LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CLAN_RANK_LIST, null, $ranks);
$ranks = modulehook('clanranks', $ranks);
$params['ranksNames'] = $ranks['ranks'];

if ('detail' == $op)
{
    $params['tpl'] = 'applicant/detail';

    require_once 'lib/clan/detail.php';
}
elseif ('list' == $op)
{
    $params['tpl'] = 'applicant/list';

    \LotgdResponse::pageTitle('title.list', [], $textDomain);

    require_once 'lib/clan/list.php';
}
elseif ('waiting' == $op)
{
    $params['tpl'] = 'applicant/waiting';

    \LotgdResponse::pageTitle('title.applicant', [], $textDomain);

    \LotgdNavigation::addHeader('category.options');

    $nav = (CLAN_APPLICANT == $session['user']['clanrank']) ? 'lobby' : 'rooms';
    \LotgdNavigation::addNav("nav.applicant.waiting.area.{$nav}", 'clan.php');
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'] && 'apply' == $op)
{
    $params['tpl'] = 'applicant/apply';

    \LotgdResponse::pageTitle('title.applicant', [], $textDomain);

    require_once 'lib/clan/applicant_apply.php';
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'] && 'new' == $op)
{
    $params['tpl'] = 'applicant/new';

    \LotgdResponse::pageTitle('title.applicant', [], $textDomain);

    require_once 'lib/clan/applicant_new.php';
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'])
{
    $params['tpl'] = 'applicant';

    \LotgdResponse::pageTitle('title.applicant', [], $textDomain);

    require_once 'lib/clan/applicant.php';
}
elseif ('' == $op)
{
    $params['tpl'] = 'default';

    \LotgdResponse::pageTitle('title.default', ['name' => \LotgdSanitize::fullSanitize($claninfo['clanname'])], $textDomain);

    require_once 'lib/clan/clan_default.php';
}
elseif ('motd' == $op)
{
    $params['tpl'] = 'motd';

    \LotgdResponse::pageTitle('title.motd', [], $textDomain);

    require_once 'lib/clan/clan_motd.php';
}
elseif ('membership' == $op)
{
    $params['tpl'] = 'membership';

    \LotgdResponse::pageTitle('title.membership', ['name' => \LotgdSanitize::fullSanitize($claninfo['clanname'])], $textDomain);

    require_once 'lib/clan/clan_membership.php';
}
elseif ('withdraw' == $op)
{
    $params['tpl'] = 'withdraw';

    require_once 'lib/clan/clan_withdraw.php';
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-clan-tpl-params', $params);
\LotgdResponse::pageAddContent(LotgdTheme::renderTheme('pages/clan.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();

function clanform()
{
    $data = [
        'clanname' => htmlentities(stripslashes(\LotgdRequest::getPost('clanname')), ENT_COMPAT, getsetting('charset', 'UTF-8')),
        'clanshort' => htmlentities(stripslashes(\LotgdRequest::getPost('clanshort')), ENT_COMPAT, getsetting('charset', 'UTF-8'))
    ];

    rawoutput(LotgdTheme::renderThemeTemplate('page/clan/new.twig', $data));
}
