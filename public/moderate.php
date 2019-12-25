<?php

require_once 'common.php';

check_su_access(SU_EDIT_COMMENTS);

$op = (string) \LotgdHttp::getQuery('op');
$area = (string) \LotgdHttp::getQuery('area');
$subop = (string) \LotgdHttp::getQuery('subop');
$seen = (int) \LotgdHttp::getQuery('seen');

$repository = \Doctrine::getRepository('LotgdCore:Commentary');
$textDomain = 'page-moderate';
$params = [
    'textDomain' => $textDomain,
    'area' => $area
];

if ($seen)
{
    $session['user']['recentcomments'] = new \DateTime('now');
}

\LotgdNavigation::superuserGrottoNav();

\LotgdNavigation::addHeader('moderate.category.other');
\LotgdNavigation::addNav('moderate.nav.overview', 'moderate.php');
\LotgdNavigation::addNav('moderate.nav.reset', 'moderate.php?seen=1');
\LotgdNavigation::addNav('moderate.nav.bios', 'bios.php');

// ($session['user']['superuser'] & SU_AUDIT_MODERATION) && \LotgdNavigation::addNav('moderate.nav.audit', 'moderate.php?op=audit');

\LotgdNavigation::addHeader('moderate.category.review');
\LotgdNavigation::addHeader('moderate.category.commentary');
\LotgdNavigation::addHeader('moderate.category.sections');

$commentary = new \Lotgd\Core\Output\Commentary();
$params['sectionName'] = $commentary->commentaryLocs();
$sections = datacache('commentary-published-sections', 1800, true);

if (! is_array($sections) || ! count($sections))
{
    $sections = $repository->getPublishedSections();

    updatedatacache('commentary-published-sections', $sections, true);
}

\LotgdNavigation::addHeader('moderate.category.sections');
(getsetting('betaperplayer', 1) && file_exists('public/pavilion.php')) && \LotgdNavigation::addNavNotl($params['sectionName']['beta'], 'moderate.php?area=beta');
($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO) && \LotgdNavigation::addNavNotl($params['sectionName']['superuser'], 'moderate.php?area=superuser');

foreach($sections as $section)
{
    \LotgdNavigation::addNavNotl($params['sectionName'][$section] ?? $section, "moderate.php?area={$section}");
}

\LotgdNavigation::addHeader('moderate.category.clan.halls');

page_header('title', [], $textDomain);

if ('' == $op)
{
    $params['tpl'] = $area ? 'area' : 'default';
}

if ($session['user']['superuser'] & SU_MODERATE_CLANS)
{
    $clanRepository = \Doctrine::getRepository('LotgdCore:Clans');
    $entities = $clanRepository->findAll();

    \LotgdNavigation::addHeader('moderate.category.clan.halls');

    foreach ($entities as $clan)
    {
        \LotgdNavigation::addNavNotl(sprintf('&lt;%s&gt; %s', $clan->getClanshort(), $clan->getClanname()), "moderate.php?area=clan-{$clan->getClanid()}");
    }
}
elseif (
    ($session['user']['superuser'] & SU_EDIT_COMMENTS)
    && getsetting('officermoderate', 0)
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

    $clanRepository = \Doctrine::getRepository('LotgdCore:Clans');
    $entity = $clanRepository->find($session['user']['clanid']);

    \LotgdNavigation::addHeader('moderate.category.clan.halls');

    if ($entity)
    {
        \LotgdNavigation::addNavNotl(sprintf('&lt;%s&gt; %s', $entity->getClanshort(), $entity->getClanname()), "moderate.php?area=clan-{$entity->getClanid()}");
    }
    else
    {
        debug('There was an error while trying to access your clan.');
    }
}

rawoutput(\LotgdTheme::renderLotgdTemplate('core/page/moderate.twig', $params));

page_footer();
