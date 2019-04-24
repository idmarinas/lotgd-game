<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/events.php';
require_once 'lib/experience.php';

tlschema('village');

//-- First check for autochallengeç
if (getsetting('automaster', 1) && 1 != $session['user']['seenmaster'])
{
    //masters hunt down truant students
    $level = $session['user']['level'] + 1;
    $dks = $session['user']['dragonkills'];
    $expreqd = exp_for_next_level($level, $dks);

    if ($session['user']['experience'] > $expreqd && $session['user']['level'] < getsetting('maxlevel', 15))
    {
        return redirect('train.php?op=autochallenge');
    }
}

// See if the user is in a valid location and if not, put them back to
// a place which is valid
$vname = getsetting('villagename', LOCATION_FIELDS);
$iname = getsetting('innname', LOCATION_INN);
$valid_loc = [];
$valid_loc[$vname] = 'village';
$valid_loc = modulehook('validlocation', $valid_loc);

// Don't hook on to this text for your standard modules please, use "village" instead.
// This hook is specifically to allow modules that do other villages to create ambience.
$result = modulehook('village-text-domain', ['textDomain' => 'page-village', 'textDomainNavigation' => 'navigation-village']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$params = [
    'village' => $vname,
    'inn' => $iname,
    'textDomain' => $textDomain
];

if (! isset($valid_loc[$session['user']['location']]))
{
    $session['user']['location'] = $vname;
}

//-- Newest player in realm
$params['newestplayer'] = (int) getsetting('newestplayer', 0);
$params['newestname'] = (string) getsetting('newestplayername', '');

$params['newtext'] = 'newestOther';
if ($params['newestplayer'] == $session['user']['acctid'])
{
    $params['newtext'] = 'newestPlayer';
    $params['newestname'] = $session['user']['name'];
}
elseif (! $params['newestname'] && $params['newestplayer'])
{
    $characterRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);
    $params['newestname'] = $characterRepository->getCharacterNameFromAcctId($params['newestplayer']) ?: 'Unknown';
    savesetting('newestplayername', $params['newestname']);
}

page_header('title', $params, $textDomain);

$skipvillagedesc = handle_event('village');
checkday();

if (1 == $session['user']['slaydragon'])
{
    $session['user']['slaydragon'] = 0;
}

if (! $session['user']['alive'])
{
    return redirect('shades.php');
}

$op = \LotgdHttp::getQuery('op');
$com = \LotgdHttp::getQuery('commentPage');
$commenting = \LotgdHttp::getQuery('commenting');
$comment = \LotgdHttp::getPost('comment');

// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
// The '1' should really be sysadmin customizable.
if (! $op && '' == $com && ! $comment && ! $commenting && 0 != module_events('village', getsetting('villagechance', 0)))
{
    if (\LotgdNavigation::checkNavs())
    {
        page_footer();
    }
    else
    {
        // Reset the special for good.
        $session['user']['specialinc'] = '';
        $session['user']['specialmisc'] = '';
        $skipvillagedesc = true;
        $op = '';
        \LotgdHttp::setQuery('op', '');
    }
}

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

//-- City gates
\LotgdNavigation::addHeader('headers.gate');
\LotgdNavigation::addNav('navs.forest', 'forest.php');

if (getsetting('pvp', 1))
{
    \LotgdNavigation::addNav('navs.pvp', 'pvp.php');
}

//-- Fields
\LotgdNavigation::addHeader('headers.fields');
\LotgdNavigation::addNav('navs.logout', 'login.php?op=logout');

if (getsetting('enablecompanions', true))
{
    \LotgdNavigation::addNav('navs.mercenarycamp', 'mercenarycamp.php');
}

//-- Fight street
\LotgdNavigation::addHeader('headers.fight');
\LotgdNavigation::addNav('navs.train', 'train.php');

if (file_exists('lodge.php'))
{
    \LotgdNavigation::addNav('navs.lodge', 'lodge.php');
}

//-- Market street
\LotgdNavigation::addHeader('headers.market');
\LotgdNavigation::addNav('navs.weaponshop', 'weapons.php');
\LotgdNavigation::addNav('navs.armorshop', 'armor.php');
\LotgdNavigation::addNav('navs.bank', 'bank.php');
\LotgdNavigation::addNav('navs.gypsy', 'gypsy.php');

if (1 == getsetting('betaperplayer', 1) && file_exists('pavilion.php'))
{
    \LotgdNavigation::addNav('navs.pavilion', 'pavilion.php');
}

//-- Industrial street
\LotgdNavigation::addHeader('headers.industrial');

//-- Tavern street
\LotgdNavigation::addHeader('headers.tavern');
\LotgdNavigation::addNav('navs.innname', 'inn.php', ['params' => ['inn' => $iname]]);
\LotgdNavigation::addNav('navs.stablename', 'stables.php');
\LotgdNavigation::addNav('navs.gardens', 'gardens.php' );
\LotgdNavigation::addNav('navs.rock', 'rock.php');

if (getsetting('allowclans', 1))
{
    \LotgdNavigation::addnav('navs.clan', 'clan.php');
}

//-- Info street
\LotgdNavigation::addHeader('headers.info');
\LotgdNavigation::addNav('navs.faq', 'petition.php?op=faq', [
    'attributes' => [
        'data-force' => 'true',
        'onclick' => 'Lotgd.embed(this)'
    ]
]);
\LotgdNavigation::addNav('navs.news', 'news.php');
\LotgdNavigation::addNav('navs.list', 'list.php');
\LotgdNavigation::addNav('navs.hof', 'hof.php');

//-- Other navs
\LotgdNavigation::addHeader('headers.other');
\LotgdNavigation::addNav('navs.account', 'account.php');
\LotgdNavigation::addNav('navs.prefs', 'prefs.php');

if (! file_exists('lodge.php'))
{
    \LotgdNavigation::addNav('navs.referral', 'referral.php');
}

//-- Superuser menu
\LotgdNavigation::superuser();

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//special hook for all villages... saves queries...
modulehook('village');

$params['showVillageDesc'] = ! $skipvillagedesc; //-- Show or not village description
$params['SU_EDIT_USERS'] = $session['user']['superuser'] & SU_EDIT_USERS;
$params['blockCommentArea'] = false; //-- Show or not comment area

//-- This is only for params not use for other purpose
$params = modulehook('page-village-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/village.twig', $params));

module_display_events('village', 'village.php');

page_footer();
