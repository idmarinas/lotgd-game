<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/events.php';

//-- First check for autochallengeç
if (LotgdSetting::getSetting('automaster', 1) && 1 != $session['user']['seenmaster'])
{
    //masters hunt down truant students
    $level = $session['user']['level'] + 1;
    $dks = $session['user']['dragonkills'];
    $expreqd = \LotgdTool::expForNextLevel($level, $dks);

    if ($session['user']['experience'] > $expreqd && $session['user']['level'] < LotgdSetting::getSetting('maxlevel', 15))
    {
        redirect('train.php?op=autochallenge');
    }
}

// See if the user is in a valid location and if not, put them back to
// a place which is valid
$vname = LotgdSetting::getSetting('villagename', LOCATION_FIELDS);
$iname = LotgdSetting::getSetting('innname', LOCATION_INN);
$valid_loc = [];
$valid_loc[$vname] = 'village';
$args = new GenericEvent(null, $valid_loc);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_VILLAGE_LOCATION);
$valid_loc = modulehook('validlocation', $args->getArguments());

// Don't hook on to this text for your standard modules please, use "village" instead.
// This hook is specifically to allow modules that do other villages to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_village', 'textDomainNavigation' => 'navigation_village']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_VILLAGE_PRE);
$result = modulehook('village-text-domain', $args->getArguments());
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
$params['newestplayer'] = (int) LotgdSetting::getSetting('newestplayer', 0);
$params['newestname'] = (string) LotgdSetting::getSetting('newestplayername', '');

$params['newtext'] = 'newestOther';
if ($params['newestplayer'] == $session['user']['acctid'])
{
    $params['newtext'] = 'newestPlayer';
    $params['newestname'] = $session['user']['name'];
}
elseif (! $params['newestname'] && $params['newestplayer'])
{
    $characterRepository = \Doctrine::getRepository('LotgdCore:Avatar');
    $params['newestname'] = $characterRepository->getCharacterNameFromAcctId($params['newestplayer']) ?: 'Unknown';
    LotgdSetting::saveSetting('newestplayername', $params['newestname']);
}

//-- Init page
\LotgdResponse::pageStart('title', $params, $textDomain);

$skipvillagedesc = handle_event('village');
\LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

if (1 == $session['user']['slaydragon'])
{
    $session['user']['slaydragon'] = 0;
}

if (! $session['user']['alive'])
{
    redirect('shades.php');
}

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$op = $request->query->get('op');
$com = $request->query->getInt('commentPage');
$commenting = $request->query->get('commenting');
$comment = $request->request->get('comment');

// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
// The '1' should really be sysadmin customizable.
if (! $op && '' == $com && ! $comment && ! $commenting && 0 != module_events('village', LotgdSetting::getSetting('villagechance', 0)))
{
    if (\LotgdNavigation::checkNavs())
    {
        \LotgdResponse::pageEnd();
    }
    else
    {
        // Reset the special for good.
        $session['user']['specialinc'] = '';
        $session['user']['specialmisc'] = '';
        $skipvillagedesc = true;
        $op = '';
        $request->setQuery('op', '');
    }
}

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

//-- City gates
\LotgdNavigation::addHeader('headers.gate');
\LotgdNavigation::addNav('navs.forest', 'forest.php');

if (LotgdSetting::getSetting('pvp', 1))
{
    \LotgdNavigation::addNav('navs.pvp', 'pvp.php');
}

//-- Fields
\LotgdNavigation::addHeader('headers.fields');
\LotgdNavigation::addNav('navs.logout', 'login.php?op=logout');

if (LotgdSetting::getSetting('enablecompanions', true))
{
    \LotgdNavigation::addNav('navs.mercenarycamp', 'mercenarycamp.php');
}

//-- Fight street
\LotgdNavigation::addHeader('headers.fight');
\LotgdNavigation::addNav('navs.train', 'train.php');

if (file_exists('public/lodge.php'))
{
    \LotgdNavigation::addNav('navs.lodge', 'lodge.php');
}

//-- Market street
\LotgdNavigation::addHeader('headers.market');
\LotgdNavigation::addNav('navs.weaponshop', 'weapons.php');
\LotgdNavigation::addNav('navs.armorshop', 'armor.php');
\LotgdNavigation::addNav('navs.bank', 'bank.php');
\LotgdNavigation::addNav('navs.gypsy', 'gypsy.php');

//-- Industrial street
\LotgdNavigation::addHeader('headers.industrial');

//-- Tavern street
\LotgdNavigation::addHeader('headers.tavern');
\LotgdNavigation::addNav('navs.innname', 'inn.php', ['params' => ['inn' => $iname]]);
\LotgdNavigation::addNav('navs.stablename', 'stables.php');
\LotgdNavigation::addNav('navs.gardens', 'gardens.php' );
\LotgdNavigation::addNav('navs.rock', 'rock.php');

if (LotgdSetting::getSetting('allowclans', 1))
{
    \LotgdNavigation::addnav('navs.clan', 'clan.php');
}

//-- Info street
\LotgdNavigation::addHeader('headers.info');
\LotgdNavigation::addNav('navs.faq', '#', [
    'attributes' => [
        'id' => 'village-petition-faq',
        'onclick' => "JaxonLotgd.Ajax.Core.Petition.faq(); $(this).addClass('disabled')"
    ]
]);
\LotgdNavigation::addNav('navs.news', 'news.php');
\LotgdNavigation::addNav('navs.list', 'list.php');
\LotgdNavigation::addNav('navs.hof', 'hof.php');

//-- Other navs
\LotgdNavigation::addHeader('headers.other');
\LotgdNavigation::addNav('navs.account', 'account.php');
\LotgdNavigation::addNav('navs.prefs', 'prefs.php');

if (! file_exists('public/lodge.php'))
{
    \LotgdNavigation::addNav('navs.referral', 'referral.php');
}

//-- Superuser menu
\LotgdNavigation::superuser();

//special hook for all villages... saves queries...
\LotgdEventDispatcher::dispatch(new GenericEvent(), Events::PAGE_VILLAGE);
modulehook('village');

$params['showVillageDesc'] = ! $skipvillagedesc; //-- Show or not village description
$params['SU_EDIT_USERS'] = $session['user']['superuser'] & SU_EDIT_USERS;
$params['blockCommentArea'] = false; //-- Show or not comment area
$params['commentarySection'] = 'village'; //-- Commentary section

$request->attributes->set('params', $params);

\LotgdResponse::callController(Lotgd\Core\Controller\VillageController::class);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

module_display_events('village', 'village.php');

//-- Finalize page
\LotgdResponse::pageEnd();
