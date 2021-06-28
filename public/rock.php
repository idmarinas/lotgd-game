<?php

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

\LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

// Don't hook on to this text for your standard modules please, use "rock" instead.
// This hook is specifically to allow modules that do other rocks to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_rock', 'textDomainNavigation' => 'navigation_rock']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_ROCK_PRE);
$result = modulehook('rock-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::villageNav();

$params = [
    'textDomain' => $textDomain
];

$params['tpl'] = 'default';
$title = 'title.default';

if ($session['user']['dragonkills'] > 0 || $session['user']['superuser'] & SU_EDIT_COMMENTS)
{
    $params['tpl'] = 'veteran';
    $title = 'title.veteran';
}

//-- Init page
\LotgdResponse::pageStart($title, [], $textDomain);

$request->attributes->set('params', $params);

\LotgdResponse::callController(Lotgd\Core\Controller\RockController::class);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();

