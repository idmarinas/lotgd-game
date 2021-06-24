<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/buffs.php';

// Don't hook on to this text for your standard modules please, use "stable" instead.
// This hook is specifically to allow modules that do other stables to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_stables', 'textDomainNavigation' => 'navigation_stables']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_STABLES_PRE);
$result = modulehook('stables-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$playermount = getmount($session['user']['hashorse']);

$confirm = 0;

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

\LotgdNavigation::addHeader('category.other');
\LotgdNavigation::villageNav();

$repaygold = 0;
$repaygems = 0;
$grubprice = 0;

if (! empty($playermount))
{
    $repaygold = round($playermount['mountcostgold'] * 2 / 3, 0);
    $repaygems = round($playermount['mountcostgems'] * 2 / 3, 0);
    $grubprice = round($session['user']['level'] * $playermount['mountfeedcost'], 0);
}

$params = [
    'textDomain' => $textDomain,
    'barkeep' => getsetting('barkeep', '`tCedrik`0'),
    'userSex' => $session['user']['sex'],
    'player_mount' => $playermount,
    'mountName' => $playermount['mountname'] ?? '',
    'confirm' => 0,
    'repaygems' => $repaygems,
    'repaygold' => $repaygold,
    'grubprice' => $grubprice
];

$op = (string) \LotgdRequest::getQuery('op');
$mountId = $request->query->getInt('id');

$mountRepository = \Doctrine::getRepository('LotgdCore:Mounts');

if ('' == $op)
{
    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    $method = 'index';
}
elseif ('examine' == $op)
{
    $method = 'examine';
}
elseif ('buymount' == $op)
{
    $method = 'buy';
}

if ('confirmbuy' == $op)
{
    $method = 'buyConfirm';
}
elseif ('feed' == $op)
{
    $method = 'feed';
}
elseif ('sellmount' == $op)
{
    $method = 'sell';
}
elseif ('confirmsell' == $op)
{
    $method = 'sellConfirm';
}

$params['confirm'] = $confirm;

$request->attributes->set('params', $params);

\LotgdResponse::callController(Lotgd\Core\Controller\StableController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
