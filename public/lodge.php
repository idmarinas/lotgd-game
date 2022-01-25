<?php

use Lotgd\Core\Controller\LodgeController;
use Lotgd\Core\Events;
// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Http\Request;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

// Don't hook on to this text for your standard modules please, use "lodge" instead.
// This hook is specifically to allow modules that do other lodges to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_lodge', 'textDomainNavigation' => 'navigation_lodge']);
LotgdEventDispatcher::dispatch($args, Events::PAGE_LODGE_PRE);
$result               = modulehook('lodge-text-domain', $args->getArguments());
$textDomain           = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request $request */
$request = LotgdKernel::get(Request::class);

$op = (string) $request->query->get('op');

if ('' == $op)
{
    LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
}

$pointsavailable = max(0, $session['user']['donation'] - $session['user']['donationspent']);
//-- Have access to Lodge
$entry = ($session['user']['donation'] > 0) || ($session['user']['superuser'] & SU_EDIT_COMMENTS);

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain'      => $textDomain,
    'pointsAvailable' => $pointsavailable,
    'innName'         => LotgdSetting::getSetting('innname', LOCATION_INN),
    'barkeep'         => LotgdSetting::getSetting('barkeep', '`tCedrik`0'),
    'canEntry'        => $entry,
];

//-- Change text domain for navigation
LotgdNavigation::setTextDomain($textDomainNavigation);

LotgdNavigation::addHeader('category.navigation');
LotgdNavigation::villageNav();

LotgdNavigation::addHeader('category.general');
if ('' != $op && $entry)
{
    LotgdNavigation::addnav('navs.return', 'lodge.php');
}

LotgdNavigation::addnav('navs.referral', 'referral.php');
LotgdNavigation::addnav('navs.desc', 'lodge.php?op=points');

if ('' == $op)
{
    $method = 'index';
}
elseif ('points' == $op)
{
    $method = 'points';
}

$request->attributes->set('params', $params);

LotgdResponse::callController(LodgeController::class, $method);

//-- Restore text domain for navigation
LotgdNavigation::setTextDomain();

//-- Finalize page
LotgdResponse::pageEnd();
