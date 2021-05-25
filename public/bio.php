<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Event\Clan;

require_once 'common.php';

$textDomain = 'page_bio';
checkday();

$ret = \LotgdRequest::getQuery('ret');
$char = \LotgdRequest::getQuery('char');

$return = 'list.php';
if ($ret)
{
    $return = preg_replace('/[&?]c=[[:digit:]]+/', '', $ret);
    $return = trim($return, '/');
}

$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);

//-- Legacy support
if (! \is_numeric($char))
{
    $char = $repository->getAcctIdFromLogin($char);
}
$target = $repository->getCharacterInfoFromAcctId((int) $char);
$recentNews = $repository->getCharacterNewsFromAcctId((int) $char);

if (empty($target))
{
    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('deleted', [], $textDomain));

    return redirect($return);
}

$ranks = [
    CLAN_APPLICANT => 'ranks.00',
    CLAN_MEMBER => 'ranks.010',
    CLAN_OFFICER => 'ranks.020',
    CLAN_ADMINISTRATIVE => 'ranks.025',
    CLAN_LEADER => 'ranks.030',
    CLAN_FOUNDER => 'ranks.031'
];

$ranks = new Clan(['ranks' => $ranks, 'textDomain' => 'page_clan', 'clanid' => $target['clanid']]);
\LotgdEventDispatcher::dispatch($ranks, Clan::RANK_LIST);
$ranks = modulehook('clanranks', $ranks->getData());

$args = ['' => \LotgdTranslator::t('character.specialtyname', [], 'app_default')];
\LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_SPECIALTY_NAMES, null, $args);
$specialties = modulehook('specialtynames', $args);

$params = [
    'textDomain' => $textDomain,
    'character' => $target,
    'recentNews' => $recentNews,
    'ranks' => $ranks['ranks'],
    'specialties' => $specialties,
    'RACE_UNKNOWN' => RACE_UNKNOWN
];

//-- Init page
\LotgdResponse::pageStart('title', [ 'name' => \LotgdSanitize::fullSanitize($target['name']) ], $textDomain);

\LotgdNavigation::addHeader('common.category.return');

if ($session['user']['superuser'] & SU_EDIT_USERS)
{
    \LotgdNavigation::addHeader('common.superuser.category');
    \LotgdNavigation::addNav('bio.nav.user', "user.php?op=edit&userid=$char");
}

\LotgdNavigation::addHeader('common.category.return');
if ('' == $ret)
{
    \LotgdNavigation::addNav('bio.nav.list', $return);
}
elseif ('list.php' == $return)
{
    \LotgdNavigation::addNav('bio.nav.list', $return);
}
else
{
    \LotgdNavigation::addNav('bio.nav.whence', $return);
    \LotgdNavigation::addNav('bio.nav.village', 'village.php');
}
//-- This is only for params not use for other purpose
$params = modulehook('page-bio-tpl-params', $params);
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/bio.html.twig', $params));

\LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_OTHER_BIO_END, null, $target);
modulehook('bioend', $target);

//-- Finalize page
\LotgdResponse::pageEnd();
