<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/nltoappon.php';
require_once 'lib/commentary.php';
require_once 'lib/systemmail.php';
require_once 'lib/sanitize.php';

// Don't hook on to this text for your standard modules please, use "clan" instead.
// This hook is specifically to allow modules that do other clans to create ambience.
$result = modulehook('clan-text-domain', ['textDomain' => 'page-clan', 'textDomainNavigation' => 'navigation-clan']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$op = (string) \LotgdHttp::getQuery('op');

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

$ranks = [
    CLAN_APPLICANT => 'ranks.00',
    CLAN_MEMBER => 'ranks.010',
    CLAN_OFFICER => 'ranks.020',
    CLAN_ADMINISTRATIVE => 'ranks.025',
    CLAN_LEADER => 'ranks.030',
    CLAN_FOUNDER => 'ranks.031'
];

$ranks = modulehook('clanranks', ['ranks' => $ranks, 'clanid' => $session['user']['clanid']]);
$params['ranksNames'] = $ranks['ranks'];

if ('detail' == $op)
{
    $params['tpl'] = 'applicant/detail';

    page_header('title.detail', [], $textDomain);

    require_once 'lib/clan/detail.php';
}
elseif ('list' == $op)
{
    $params['tpl'] = 'applicant/list';

    page_header('title.list', [], $textDomain);

    require_once 'lib/clan/list.php';
}
elseif ('waiting' == $op)
{
    $params['tpl'] = 'applicant/waiting';

    page_header('title.applicant', [], $textDomain);

    \LotgdNavigation::addHeader('category.options');

    $nav = (CLAN_APPLICANT == $session['user']['clanrank']) ? 'lobby' : 'rooms';
    \LotgdNavigation::addNav("nav.applicant.waiting.area.{$nav}", 'clan.php');
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'] && 'apply' == $op)
{
    $params['tpl'] = 'applicant/apply';

    page_header('title.applicant', [], $textDomain);

    require_once 'lib/clan/applicant_apply.php';
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'] && 'new' == $op)
{
    $params['tpl'] = 'applicant/new';

    page_header('title.applicant', [], $textDomain);

    require_once 'lib/clan/applicant_new.php';
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'])
{
    $params['tpl'] = 'applicant';

    page_header('title.applicant', [], $textDomain);

    require_once 'lib/clan/applicant.php';
}
elseif ('' == $op)
{
    $params['tpl'] = 'default';

    page_header('title.default', ['name' => \LotgdSanitize::fullSanitize($claninfo['clanname'])], $textDomain);

    require_once 'lib/clan/clan_default.php';
}
elseif ('motd' == $op)
{
    $params['tpl'] = 'motd';

    page_header('title.motd', [], $textDomain);

    require_once 'lib/clan/clan_motd.php';
}
elseif ('membership' == $op)
{
    $params['tpl'] = 'membership';

    page_header('title.membership', ['name' => \LotgdSanitize::fullSanitize($claninfo['clanname'])], $textDomain);

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
rawoutput(LotgdTheme::renderThemeTemplate('page/clan.twig', $params));

page_footer();

function clanform()
{
    $data = [
        'clanname' => htmlentities(stripslashes(httppost('clanname')), ENT_COMPAT, getsetting('charset', 'UTF-8')),
        'clanshort' => htmlentities(stripslashes(httppost('clanshort')), ENT_COMPAT, getsetting('charset', 'UTF-8'))
    ];

    rawoutput(LotgdTheme::renderThemeTemplate('page/clan/new.twig', $data));
}
