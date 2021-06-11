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

if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
{
    \LotgdNavigation::addHeader('category.heal.potion');
    \LotgdNavigation::addNav('nav.heal.complete', "healer.php?op=buy&pct=100&return={$return}");

    for ($i = 90; $i > 0; $i -= 10)
    {
        \LotgdNavigation::addNav('nav.heal.percent', "healer.php?op=buy&pct={$i}&return={$return}", [
            'params' => [
                'percent' => $i / 100,
                'cost' => round($cost * ($i / 100), 0)
            ]
        ]);
    }
    \LotgdEventDispatcher::dispatch(new GenericEvent(), Events::PAGE_HEALER_POTION);
    modulehook('potion');
}
\LotgdNavigation::addHeader('category.heal.companion');

foreach ($companions as $name => $companion)
{
    if ($companion['cannotbehealed'] ?? false)
    {
        continue;
    }

    $points = $companion['maxhitpoints'] - $companion['hitpoints'];

    if ($points > 0)
    {
        $name = rawurlencode($name);
        $compcost = round(log($session['user']['level'] + 1) * ($points + 10) * 1.33);
        \LotgdNavigation::addNav('nav.heal.companion', "healer.php?op=companion&name={$name}&compcost={$compcost}&return={$return}", [
            'params' => [
                'companionName' => $companion['name'],
                'cost' => $compcost
            ]
        ]);
    }
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

\LotgdResponse::callController(\Lotgd\Core\Controller\HealerController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
