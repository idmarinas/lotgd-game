<?php

use Lotgd\Core\Controller\InnController;
use Lotgd\Core\Events;
// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Http\Request;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

// Don't hook on to this text for your standard modules please, use "inn" instead.
// This hook is specifically to allow modules that do other inns to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_inn', 'textDomainNavigation' => 'navigation_inn']);
LotgdEventDispatcher::dispatch($args, Events::PAGE_INN_PRE);
$result               = $args->getArguments();
$textDomain           = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$skipinndesc = false;

/** @var Lotgd\Core\Http\Request $request */
$request = LotgdKernel::get(Request::class);

$params = [
    'textDomain'           => $textDomain,
    'innName'              => LotgdSetting::getSetting('innname', LOCATION_INN),
    'villageName'          => LotgdSetting::getSetting('villagename', LOCATION_FIELDS),
    'barkeep'              => LotgdSetting::getSetting('barkeep', '`tCedrik`0'),
    'partner'              => LotgdTool::getPartner(),
    'showInnDescription'   => ! $skipinndesc,
    'includeTemplatesPre'  => [], //-- Templates that are in top of content (but below of title)
    'includeTemplatesPost' => [], //-- Templates that are in bottom of content
];

//-- Init page
LotgdResponse::pageStart('title', ['name' => LotgdSanitize::fullSanitize($params['innName'])], $textDomain);

$op         = (string) $request->query->get('op');
$subop      = (string) $request->query->get('subop');
$com        = $request->query->getInt('commentPage');
$commenting = $request->query->get('commenting');
$comment    = $request->request->get('comment');

$params['op'] = $op;

// Correctly reset the location if they fleeing the dragon
// This needs to be done up here because a special could alter your op.
if ('fleedragon' == $op)
{
    $session['user']['location'] = $params['villageName'];
}

//-- Change text domain for navigation
LotgdNavigation::setTextDomain($textDomainNavigation);

LotgdNavigation::addHeader('category.other');
LotgdNavigation::villageNav();

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

        LotgdNavigation::blockLink('inn.php');

        // Don't give people a chance at a special event if they are just browsing
        // the commentary (or talking) or dealing with any of the hooks in the inn.
        if ('fleedragon' != $op && '' == $com && ! $comment && ! $commenting)
        {
            /** New occurrence dispatcher for special events. */
            /** @var \Lotgd\CoreBundle\OccurrenceBundle\OccurrenceEvent $event */
            $event = LotgdKernel::get('occurrence_dispatcher')->dispatch('inn', null, [
                'translation_domain'            => $textDomain,
                'translation_domain_navigation' => $textDomainNavigation,
                'route'                         => 'inn.php',
                'navigation_method'             => 'innNav',
            ]);

            if ($event->isPropagationStopped())
            {
                LotgdResponse::pageEnd();
            }
            elseif ($event['skip_description'])
            {
                $skipinndesc = true;

                $op = '';
                LotgdRequest::setQuery('op', '');
            }
            //-- Only execute when NOT occurrence is in progress.
            elseif (0 != module_events('inn', LotgdSetting::getSetting('innchance', 0)))
            {
                if (LotgdNavigation::checkNavs())
                {
                    LotgdResponse::pageEnd();
                }

                // Reset the special for good.
                $session['user']['specialinc']  = '';
                $session['user']['specialmisc'] = '';
                $skipinndesc                    = true;

                $op = '';
                LotgdRequest::setQuery('op', '');
            }
        }

    break;
}

$params['showInnDescription'] = ! $skipinndesc;

if ( ! $skipinndesc)
{
    LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
}

$request->attributes->set('params', $params);

LotgdResponse::callController(InnController::class, $method);

//-- Restore text domain for navigation
LotgdNavigation::setTextDomain();
if ('default' == $params['tpl'])
{
    $args = new GenericEvent();
    LotgdEventDispatcher::dispatch($args, Events::PAGE_INN);

    module_display_events('inn', 'inn.php');
}

//-- Finalize page
LotgdResponse::pageEnd();
