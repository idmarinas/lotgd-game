<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

\LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

// Don't hook on to this text for your standard modules please, use "weapon" instead.
// This hook is specifically to allow modules that do other weapons to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_weapon', 'textDomainNavigation' => 'navigation_weapon']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_WEAPONS_PRE);
$result = modulehook('weapon-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$tradeinvalue = round(($session['user']['weaponvalue'] * .75), 0);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$params = [
    'textDomain' => $textDomain,
    'tradeinvalue' => $tradeinvalue,
];

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$op = (string) $request->query->get('op');

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

$method = 'index';

if ('buy' == $op)
{
    $method = 'buy';
}

\LotgdNavigation::villageNav();

$request->attributes->set('params', $params);

LotgdResponse::callController(Lotgd\Core\Controller\WeaponController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
