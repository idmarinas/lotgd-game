<?php

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

checkday();

// Don't hook on to this text for your standard modules please, use "rock" instead.
// This hook is specifically to allow modules that do other rocks to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_rock', 'textDomainNavigation' => 'navigation_rock']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_ROCK_PRE);
$result = modulehook('rock-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

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

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_ROCK_POST);
$params = modulehook('page-rock-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/rock.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();

