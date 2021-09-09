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
    $level   = $session['user']['level'] + 1;
    $dks     = $session['user']['dragonkills'];
    $expreqd = \LotgdTool::expForNextLevel($level, $dks);

    if ($session['user']['experience'] > $expreqd && $session['user']['level'] < LotgdSetting::getSetting('maxlevel', 15))
    {
        redirect('train.php?op=autochallenge');
    }
}

// See if the user is in a valid location and if not, put them back to
// a place which is valid
$vname             = LotgdSetting::getSetting('villagename', LOCATION_FIELDS);
$iname             = LotgdSetting::getSetting('innname', LOCATION_INN);
$valid_loc         = [];
$valid_loc[$vname] = 'village';
$args              = new GenericEvent(null, $valid_loc);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_VILLAGE_LOCATION);
$valid_loc = modulehook('validlocation', $args->getArguments());

// Don't hook on to this text for your standard modules please, use "village" instead.
// This hook is specifically to allow modules that do other villages to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_village', 'textDomainNavigation' => 'navigation_village']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_VILLAGE_PRE);
$result               = modulehook('village-text-domain', $args->getArguments());
$textDomain           = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$params = [
    'village'    => $vname,
    'inn'        => $iname,
    'textDomain' => $textDomain,
];

if ( ! isset($valid_loc[$session['user']['location']]))
{
    $session['user']['location'] = $vname;
}

//-- Newest player in realm
$params['newestplayer'] = (int) LotgdSetting::getSetting('newestplayer', 0);
$params['newestname']   = (string) LotgdSetting::getSetting('newestplayername', '');

$params['newtext'] = 'newestOther';
if ($params['newestplayer'] == $session['user']['acctid'])
{
    $params['newtext']    = 'newestPlayer';
    $params['newestname'] = $session['user']['name'];
}
elseif ( ! $params['newestname'] && $params['newestplayer'])
{
    $characterRepository  = \Doctrine::getRepository('LotgdCore:Avatar');
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

if ( ! $session['user']['alive'])
{
    redirect('shades.php');
}

/** @var Lotgd\Core\Http\Request $request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$op         = $request->query->get('op');
$com        = $request->query->getInt('commentPage');
$commenting = $request->query->get('commenting');
$comment    = $request->request->get('comment');

// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
// The '1' should really be sysadmin customizable.
if ( ! $op && '' == $com && ! $comment && ! $commenting)
{
    /** New occurrence dispatcher for special events. */
    /** @var \Symfony\Component\EventDispatcher\GenericEvent $event */
    $event = \LotgdKernel::get('occurrence_dispatcher')->dispatch('village', null, [
        'translation_domain'            => $textDomain,
        'translation_domain_navigation' => $textDomainNavigation,
        'route'                         => 'village.php',
        'navigation_method'             => 'townNav',
    ]);

    if ($event->isPropagationStopped())
    {
        \LotgdResponse::pageEnd();
    }
    elseif ($event['skip_description'])
    {
        $skipvillagedesc = true;

        $op = '';
        $request->setQuery('op', '');
    }
    //-- Only execute when NOT occurrence is in progress.
    elseif (0 != module_events('village', LotgdSetting::getSetting('villagechance', 0)))
    {
        if (\LotgdNavigation::checkNavs())
        {
            \LotgdResponse::pageEnd();
        }
        else
        {
            // Reset the special for good.
            $session['user']['specialinc']  = '';
            $session['user']['specialmisc'] = '';
            $skipvillagedesc                = true;

            $op = '';
            $request->setQuery('op', '');
        }
    }
}

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::townNav($textDomainNavigation);

//special hook for all villages... saves queries...
\LotgdEventDispatcher::dispatch(new GenericEvent(), Events::PAGE_VILLAGE);
modulehook('village');

$params['showVillageDesc']   = ! $skipvillagedesc; //-- Show or not village description
$params['SU_EDIT_USERS']     = $session['user']['superuser'] & SU_EDIT_USERS;
$params['blockCommentArea']  = false; //-- Show or not comment area
$params['commentarySection'] = 'village'; //-- Commentary section

$request->attributes->set('params', $params);

\LotgdResponse::callController(Lotgd\Core\Controller\VillageController::class);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

module_display_events('village', 'village.php');

//-- Finalize page
\LotgdResponse::pageEnd();
