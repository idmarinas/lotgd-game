<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/pvpwarning.php';
require_once 'lib/buffs.php';
require_once 'lib/events.php';
require_once 'lib/partner.php';

// Don't hook on to this text for your standard modules please, use "inn" instead.
// This hook is specifically to allow modules that do other inns to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_inn', 'textDomainNavigation' => 'navigation_inn']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_INN_PRE);
$result = modulehook('inn-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$skipinndesc = handle_event('inn');

if (! $skipinndesc)
{
    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
}

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$params = [
    'textDomain' => $textDomain,
    'innName' => getsetting('innname', LOCATION_INN),
    'villageName' => getsetting('villagename', LOCATION_FIELDS),
    'barkeep' => getsetting('barkeep', '`tCedrik`0'),
    'partner' => get_partner(),
    'showInnDescription' => ! $skipinndesc,
    'includeTemplatesPre' => [], //-- Templates that are in top of content (but below of title)
    'includeTemplatesPost' => [] //-- Templates that are in bottom of content
];

//-- Init page
\LotgdResponse::pageStart('title', ['name' => \LotgdSanitize::fullSanitize($params['innName'])], $textDomain);

$op = (string) $request->query->get('op');
$subop = (string) $request->query->get('subop');
$com = $request->query->getInt('commentPage');
$commenting = $request->query->get('commenting');
$comment = $request->request->get('comment');

$params['op'] = $op;

// Correctly reset the location if they fleeing the dragon
// This needs to be done up here because a special could alter your op.
if ('fleedragon' == $op)
{
    $session['user']['location'] = $params['villageName'];
}

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.other');
\LotgdNavigation::villageNav();

switch ($op)
{
    case 'converse':
        $method = 'converse';
    break;
    case 'bartender':
        $method = 'bartender';
    break;
    case 'room':
        $method = 'room';
    break;
    default:
        $method = 'index';

        \LotgdNavigation::blockLink('inn.php');

        // Don't give people a chance at a special event if they are just browsing
        // the commentary (or talking) or dealing with any of the hooks in the inn.
        if ('fleedragon' != $op && '' == $com && ! $comment && ! $commenting && 0 != module_events('inn', getsetting('innchance', 0)))
        {
            if (\LotgdNavigation::checkNavs())
            {
                \LotgdResponse::pageEnd();
            }

            // Reset the special for good.
            $session['user']['specialinc'] = '';
            $session['user']['specialmisc'] = '';
            $skipinndesc = true;
            $op = '';
            \LotgdRequest::setQuery('op', '');
        }
    break;
}

$request->attributes->set('params', $params);

\LotgdResponse::callController(Lotgd\Core\Controller\InnController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();
if ('default' == $params['tpl'])
{
    $args = new GenericEvent();
    \LotgdEventDispatcher::dispatch($args, Events::PAGE_INN);
    modulehook('inn', $args->getArguments());

    module_display_events('inn', 'inn.php');
}

//-- Finalize page
\LotgdResponse::pageEnd();
