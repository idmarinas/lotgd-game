<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/events.php';

// Don't hook on to this text for your standard modules please, use "gardens" instead.
// This hook is specifically to allow modules that do other gardenss to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_gardens', 'textDomainNavigation' => 'navigation_gardens']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_GARDEN_PRE);
$result = modulehook('gardens-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$skipgardendesc = handle_event('gardens');

$params = [
    'textDomain' => $textDomain,
    'showGardenDesc' => ! $skipgardendesc,
    'includeTemplatesPre' => [],
    'includeTemplatesPost' => []
];

$op = (string) \LotgdRequest::getQuery('op');
$com = \LotgdRequest::getQuery('commentPage');
$commenting = \LotgdRequest::getQuery('commenting');
$comment = \LotgdRequest::getPost('comment');

// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
if (! $op && '' == $com && ! $comment && ! $refresh && ! $commenting && 0 != module_events('gardens', getsetting('gardenchance', 0)))
{
    if (\LotgdNavigation::checkNavs())
    {
        \LotgdResponse::pageEnd();
    }
    else
    {
        // Reset the special for good.
        $session['user']['specialinc'] = '';
        $session['user']['specialmisc'] = '';
        $skipgardendesc = true;
        $params['showGardenDesc'] = ! $skipgardendesc;
        $op = '';
        \LotgdRequest::setQuery('op', '');
    }
}

if (! $skipgardendesc)
{
    checkday();
}

\LotgdNavigation::villageNav();
$args = new GenericEvent();
\LotgdEventDispatcher::dispatch($args, Events::PAGE_GARDEN);
modulehook('gardens', $args->getArguments());

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_GARDEN_POST);
$params = modulehook('page-gardens-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/gardens.html.twig', $params));

module_display_events('gardens', 'gardens.php');

//-- Finalize page
\LotgdResponse::pageEnd();
