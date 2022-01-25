<?php

use Lotgd\Core\Output\Commentary;

require_once 'common.php';

check_su_access(SU_EDIT_COMMENTS);

$op    = (string) LotgdRequest::getQuery('op');
$area  = (string) LotgdRequest::getQuery('area');
$subop = (string) LotgdRequest::getQuery('subop');
$seen  = (int) LotgdRequest::getQuery('seen');

$repository = Doctrine::getRepository('LotgdCore:Commentary');
$textDomain = 'grotto_moderate';
$params     = [
    'textDomain' => $textDomain,
    'area'       => $area,
];

if (0 !== $seen)
{
    $session['user']['recentcomments'] = new DateTime('now');
}

LotgdNavigation::superuserGrottoNav();

LotgdNavigation::addHeader('moderate.category.other');
LotgdNavigation::addNav('moderate.nav.overview', 'moderate.php');
LotgdNavigation::addNav('moderate.nav.reset', 'moderate.php?seen=1');
LotgdNavigation::addNav('moderate.nav.bios', 'bios.php');

// ($session['user']['superuser'] & SU_AUDIT_MODERATION) && \LotgdNavigation::addNav('moderate.nav.audit', 'moderate.php?op=audit');

LotgdNavigation::addHeader('moderate.category.review');
LotgdNavigation::addHeader('moderate.category.commentary');
LotgdNavigation::addHeader('moderate.category.sections');

$commentary            = LotgdKernel::get(Commentary::class);
$params['sectionName'] = $commentary->commentaryLocs();
$cache                 = LotgdKernel::get('cache.app');
$item                  = $cache->getItem('commentary-published-sections');

if ( ! $item->isHit())
{
    $sections = $repository->getPublishedSections();

    $item->set($sections);
    $cache->save($item);
}

$sections = $item->get();

LotgdNavigation::addHeader('moderate.category.sections');
($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO) && LotgdNavigation::addNavNotl($params['sectionName']['superuser'], 'moderate.php?area=superuser');

foreach ($sections as $section)
{
    LotgdNavigation::addNavNotl($params['sectionName'][$section] ?? $section, "moderate.php?area={$section}");
}

LotgdNavigation::addHeader('moderate.category.clan.halls');

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

if ('' == $op)
{
    $params['tpl'] = '' !== $area && '0' !== $area ? 'area' : 'default';
}

if (($session['user']['superuser'] & SU_MODERATE_CLANS) !== 0)
{
    $clanRepository = Doctrine::getRepository('LotgdCore:Clans');
    $entities       = $clanRepository->findAll();

    LotgdNavigation::addHeader('moderate.category.clan.halls');

    foreach ($entities as $clan)
    {
        LotgdNavigation::addNavNotl(sprintf('&lt;%s&gt; %s', $clan->getClanshort(), $clan->getClanname()), "moderate.php?area=clan-{$clan->getClanid()}");
    }
}
elseif (
    ($session['user']['superuser'] & SU_EDIT_COMMENTS)
    && LotgdSetting::getSetting('officermoderate', 0)
    && $session['user']['clanid']
    && $session['user']['clanrank'] >= CLAN_OFFICER
) {
    // the CLAN_OFFICER requirement was chosen so that moderators couldn't
    // just get accepted as a member to any random clan and then proceed to
    // wreak havoc.
    // although this isn't really a big deal on most servers, the choice was
    // made so that staff won't have to have another issue to take into
    // consideration when choosing moderators.  the issue is moot in most
    // cases, as players that are trusted with moderator powers are also
    // often trusted with at least the rank of officer in their respective
    // clans.

    $clanRepository = Doctrine::getRepository('LotgdCore:Clans');
    $entity         = $clanRepository->find($session['user']['clanid']);

    LotgdNavigation::addHeader('moderate.category.clan.halls');

    if ($entity)
    {
        LotgdNavigation::addNavNotl(sprintf('&lt;%s&gt; %s', $entity->getClanshort(), $entity->getClanname()), "moderate.php?area=clan-{$entity->getClanid()}");
    }
    else
    {
        LotgdResponse::pageDebug('There was an error while trying to access your clan.');
    }
}

LotgdResponse::pageAddContent(LotgdTheme::render('admin/page/moderate.html.twig', $params));

//-- Finalize page
LotgdResponse::pageEnd();
