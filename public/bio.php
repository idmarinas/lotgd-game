<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/sanitize.php';

tlschema('bio');

$textDomain = 'page-bio';
checkday();

$ret = \LotgdHttp::getQuery('ret');
$char = \LotgdHttp::getQuery('char');

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

$ranks = modulehook('clanranks', ['ranks' => $ranks, 'clanid' => $target['clanid']]);

$specialties = modulehook('specialtynames', ['' => \LotgdTranslator::t('character.specialtyname', [], 'app-default')]);

$params = [
    'textDomain' => $textDomain,
    'character' => $target,
    'recentNews' => $recentNews,
    'ranks' => $ranks['ranks'],
    'specialties' => $specialties,
    'RACE_UNKNOWN' => RACE_UNKNOWN
];

page_header('title', [ 'name' => full_sanitize($target['name']) ], $textDomain);

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
rawoutput(LotgdTheme::renderThemeTemplate('page/bio.twig', $params));

modulehook('bioend', $target);

page_footer();
