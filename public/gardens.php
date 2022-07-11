<?php

use Lotgd\Core\Controller\GardenController;
use Lotgd\Core\Events;
// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Http\Request;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/events.php';

/** @var Lotgd\Core\Http\Request $request */
$request = LotgdKernel::get(Request::class);

// Don't hook on to this text for your standard modules please, use "gardens" instead.
// This hook is specifically to allow modules that do other gardenss to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_gardens', 'textDomainNavigation' => 'navigation_gardens']);
LotgdEventDispatcher::dispatch($args, Events::PAGE_GARDEN_PRE);
$result               = $args->getArguments();
$textDomain           = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$skipgardendesc = handle_event('gardens');

$params = [
    'textDomain'           => $textDomain,
    'showGardenDesc'       => ! $skipgardendesc,
    'includeTemplatesPre'  => [],
    'includeTemplatesPost' => [],
];

$op         = (string) $request->query->get('op');
$com        = $request->query->get('commentPage');
$commenting = $request->query->get('commenting');
$comment    = $request->request->get('comment');

// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
if ( ! $op && '' == $com && ! $comment && ! $refresh && ! $commenting)
{
    /** New occurrence dispatcher for special events. */
    /** @var \Lotgd\CoreBundle\OccurrenceBundle\OccurrenceEvent $event */
    $event = LotgdKernel::get('occurrence_dispatcher')->dispatch('gardens', null, [
        'translation_domain'            => $textDomain,
        'translation_domain_navigation' => $textDomainNavigation,
        'route'                         => 'gardens.php',
        'navigation_method'             => 'gardensNav',
    ]);
    if ($event->isPropagationStopped())
    {
        LotgdResponse::pageEnd();
    }
    elseif ($event['skip_description'])
    {
        $skipgardendesc = true;
        $op             = '';
        $request->query->set('op', '');
    }
    //-- Only execute when NOT occurrence is in progress.
    elseif (0 != module_events('gardens', LotgdSetting::getSetting('gardenchance', 0)))
    {
        if (LotgdNavigation::checkNavs())
        {
            LotgdResponse::pageEnd();
        }
        else
        {
            // Reset the special for good.
            $session['user']['specialinc']  = '';
            $session['user']['specialmisc'] = '';

            $skipgardendesc = true;
            $op             = '';
            $request->query->set('op', '');
        }
    }
}

if ( ! $skipgardendesc)
{
    LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
}

$params['showGardenDesc'] = ! $skipgardendesc;

LotgdNavigation::gardensNav();

$request->attributes->set('params', $params);

LotgdResponse::callController(GardenController::class);

module_display_events('gardens', 'gardens.php');

//-- Finalize page
LotgdResponse::pageEnd();
