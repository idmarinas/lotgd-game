<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

checkday();

// Don't hook on to this text for your standard modules please, use "armor" instead.
// This hook is specifically to allow modules that do other armors to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_armor', 'textDomainNavigation' => 'navigation_armor']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_ARMOR_PRE);
$result = modulehook('armor-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$tradeinvalue = round(($session['user']['armorvalue'] * .75), 0);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$params = [
    'textDomain' => $textDomain,
    'tradeinvalue' => $tradeinvalue,
];

$request->attributes->set('params', $params);

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

$op = $request->query->get('op');
$repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Armor::class);

$method = 'index';
if ('buy' == $op)
{
    $method = 'buy';
}

\LotgdNavigation::villageNav();

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

\LotgdResponse::callController(\Lotgd\Core\Controller\ArmorController::class, $method);

//-- Finalize page
\LotgdResponse::pageEnd();
