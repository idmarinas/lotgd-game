<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

checkday();

// Don't hook on to this text for your standard modules please, use "inn" instead.
// This hook is specifically to allow modules that do other inns to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_mercenarycamp', 'textDomainNavigation' => 'navigation_mercenarycamp']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_MERCENARY_CAMP_PRE);
$result = modulehook('mercenarycamp-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.navigation');

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
];

$op = (string) $request->query->get('op');

$method = 'index';

if ('heal' == $op)
{
    $method = 'heal';
}
elseif ('buy' == $op)
{
    $method = 'buy';
}

\LotgdNavigation::addHeader('category.navigation');
\LotgdNavigation::villageNav();

$request->attributes->set('params', $params);

\LotgdResponse::callController(Lotgd\Core\Controller\MercenaryCampController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
