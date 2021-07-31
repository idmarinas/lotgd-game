<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

// Don't hook on to this text for your standard modules please, use "healer" instead.
// This hook is specifically to allow modules that do other healers to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_healer', 'textDomainNavigation' => 'navigation_healer']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_HEALER_PRE);
$result = modulehook('healer-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

//-- Calculate cost for healing
$cost = log($session['user']['level']) * (($session['user']['maxhitpoints'] - $session['user']['hitpoints']) + 10);
$args = new GenericEvent(null, ['alterpct' => 1.0, 'cost' => $cost]);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_HEALER_MULTIPLY);
$result = modulehook('healmultiply', $args->getArguments());
$cost = round($result['alterpct'] * $result['cost'], 0);

$params = [
    'textDomain' => $textDomain,
    'healCost' => $cost
];

$request->attributes->set('params', $params);

$op = (string) $request->query->get('op');
$return = (string) $request->query->get('return');

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

$method = 'index';
if ('buy' == $op)
{
    $method = 'buy';
}
elseif ('companion' == $op)
{
    $method = 'companion';
}

\LotgdNavigation::addHeader('category.return');

if ('' == $return)
{
    \LotgdNavigation::addNav('nav.return.forest', 'forest.php');
    \LotgdNavigation::villageNav();
}
elseif ('village.php' == $return)
{
    \LotgdNavigation::villageNav();
}
else
{
    \LotgdNavigation::addNav('nav.return.return', $return);
}

$params['return'] = $return;

$request->attributes->set('params', $params);

\LotgdResponse::callController(\Lotgd\Core\Controller\HealerController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
