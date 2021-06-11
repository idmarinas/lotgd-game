<?php

// translator ready
// addnews ready
// mail ready

// New Hall of Fame features by anpera
// http://www.anpera.net/forum/viewforum.php?f=27

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

checkday();

// Don't hook on to this text for your standard modules please, use "hof" instead.
// This hook is specifically to allow modules that do other hofs to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_hof', 'textDomainNavigation' => 'navigation_hof']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_HOF_PRE);
$result = modulehook('hof-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

$op = (string) $request->query->get('op');
$subop = (string) $request->query->get('subop');
$page = $request->query->getInt('page');
$subop = $subop ?: 'best';
$op = $op ?: 'kills';
$order = ('worst' == $subop) ? 'ASC' : 'DESC';

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.navigation');
\LotgdNavigation::villageNav();

\LotgdNavigation::addHeader('category.ranking');
\LotgdNavigation::addNav('nav.dragonkill', "hof.php?op=kills&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.gold', "hof.php?op=money&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.gem', "hof.php?op=gems&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.charm', "hof.php?op=charm&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.tough', "hof.php?op=tough&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.resurrect', "hof.php?op=resurrects&subop={$subop}&page=1");
\LotgdNavigation::addNav('nav.dragonspeed', "hof.php?op=days&subop={$subop}&page=1");

\LotgdNavigation::addHeader('category.sort');
\LotgdNavigation::addNav('nav.best', "hof.php?op={$op}&subop=best&page={$page}");
\LotgdNavigation::addNav('nav.worst', "hof.php?op={$op}&subop=worst&page={$page}");

\LotgdNavigation::addHeader('category.other');

$args = new GenericEvent();
\LotgdEventDispatcher::dispatch($args, Events::PAGE_HOF_ADD);
modulehook('hof-add', $args->getArguments());

$request->attributes->set('params', $params);

\LotgdResponse::callController(\Lotgd\Core\Controller\HofController::class);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
