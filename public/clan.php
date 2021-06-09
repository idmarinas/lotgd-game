<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Event\Clan;
use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

// Don't hook on to this text for your standard modules please, use "clan" instead.
// This hook is specifically to allow modules that do other clans to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_clan', 'textDomainNavigation' => 'navigation_clan']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_CLAN_PRE);
$result = modulehook('clan-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

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

$ranks = new Clan(['ranks' => $ranks, 'textDomain' => $textDomain, 'clanid' => null]);
\LotgdEventDispatcher::dispatch($ranks, Clan::RANK_LIST);
$ranks = modulehook('clanranks', $ranks->getData());
$params['ranksNames'] = $ranks['ranks'];

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);
$request->attributes->set('params', $params);

$op = (string) $request->query->get('op', '');

if ('detail' == $op)
{
    $method = 'detail';
}
elseif ('list' == $op)
{
    $method = 'list';

    \LotgdResponse::pageTitle('title.list', [], $textDomain);
}
elseif ('waiting' == $op)
{
    $method = 'waiting';

    \LotgdResponse::pageTitle('title.applicant', [], $textDomain);

    \LotgdNavigation::addHeader('category.options');

    $nav = (CLAN_APPLICANT == $session['user']['clanrank']) ? 'lobby' : 'rooms';
    \LotgdNavigation::addNav("nav.applicant.waiting.area.{$nav}", 'clan.php');
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'] && 'apply' == $op)
{
    $method = 'applicantApply';

    \LotgdResponse::pageTitle('title.applicant', [], $textDomain);

}
elseif (CLAN_APPLICANT == $session['user']['clanrank'] && 'new' == $op)
{
    $method = 'applicantNew';

    \LotgdResponse::pageTitle('title.applicant', [], $textDomain);

    \LotgdNavigation::addNav('nav.applicant.apply.lobby', 'clan.php');
}
elseif (CLAN_APPLICANT == $session['user']['clanrank'])
{
    $method = 'applicant';

    \LotgdResponse::pageTitle('title.applicant', [], $textDomain);

    \LotgdNavigation::addHeader('category.options');

    if (($claninfo['clanid'] ?? 0) > 0)
    {
        //-- Applied for membership to a clan
        \LotgdNavigation::addNav('nav.applicant.waiting.label', 'clan.php?op=waiting');
        \LotgdNavigation::addNav('nav.applicant.withdraw', 'clan.php?op=withdraw');
    }
    else
    {
        //-- Hasn't applied for membership to any clan.
        \LotgdNavigation::addNav('nav.applicant.apply.membership', 'clan.php?op=apply');
        \LotgdNavigation::addNav('nav.applicant.apply.new', 'clan.php?op=new');
    }

}
elseif ('' == $op)
{
    $method = 'index';

    \LotgdResponse::pageTitle('title.default', ['name' => \LotgdSanitize::fullSanitize($claninfo['clanname'])], $textDomain);

    \LotgdNavigation::addHeader('category.options');

    if ($session['user']['clanrank'] > CLAN_MEMBER)
    {
        \LotgdNavigation::addNav('nav.default.update', 'clan.php?op=motd');
    }

    \LotgdNavigation::addNav('nav.default.membership', 'clan.php?op=membership');
    \LotgdNavigation::addNav('nav.default.online', 'list.php?op=clan');
    \LotgdNavigation::addNav('nav.default.waiting.area', 'clan.php?op=waiting');
    \LotgdNavigation::addNav('nav.default.withdraw', 'clan.php?op=withdraw', [
        'attributes' => [
            'data-options' => \json_encode(['text' => \LotgdTranslator::t('section.withdraw.confirm', [], $textDomain)]),
            'onclick'      => 'Lotgd.confirm(this, event)',
        ],
    ]);
}
elseif ('motd' == $op)
{
    $method = 'motd';

    \LotgdResponse::pageTitle('title.motd', [], $textDomain);

    \LotgdNavigation::addHeader('category.options');
    \LotgdNavigation::addNav('nav.motd.return', 'clan.php');

}
elseif ('membership' == $op)
{
    $method = 'membership';

    \LotgdResponse::pageTitle('title.membership', ['name' => \LotgdSanitize::fullSanitize($claninfo['clanname'])], $textDomain);

    \LotgdNavigation::addHeader('category.options');
    \LotgdNavigation::addNav('nav.membership.hall', 'clan.php');
}
elseif ('withdraw' == $op)
{
    $method = 'withdraw';
}

LotgdResponse::callController(\Lotgd\Core\Controller\ClanController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
