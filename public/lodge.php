<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/names.php';

// Don't hook on to this text for your standard modules please, use "lodge" instead.
// This hook is specifically to allow modules that do other lodges to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_lodge', 'textDomainNavigation' => 'navigation_lodge']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_LODGE_PRE);
$result = modulehook('lodge-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$op = (string) \LotgdRequest::getQuery('op');

if ('' == $op)
{
    checkday();
}

$pointsavailable = max(0, $session['user']['donation'] - $session['user']['donationspent']);
//-- Have access to Lodge
$entry = ($session['user']['donation'] > 0) || ($session['user']['superuser'] & SU_EDIT_COMMENTS);

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'pointsAvailable' => $pointsavailable,
    'innName' => getsetting('innname', LOCATION_INN),
    'barkeep' => getsetting('barkeep', '`tCedrik`0'),
    'canEntry' => $entry
];

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.navigation');
\LotgdNavigation::villageNav();

\LotgdNavigation::addHeader('category.general');
if ('' != $op && $entry)
{
    \LotgdNavigation::addnav('navs.return', 'lodge.php');
}

\LotgdNavigation::addnav('navs.referral', 'referral.php');
\LotgdNavigation::addnav('navs.desc', 'lodge.php?op=points');

if ('' == $op)
{
    $params['tpl'] = 'default';

    if ($entry)
    {
        \LotgdNavigation::addHeader('category.use.points');
        \LotgdEventDispatcher::dispatch(new GenericEvent(), Events::PAGE_LODGE);
        modulehook('lodge');
    }
}
elseif ('points' == $op)
{
    $params['tpl'] = 'points';

    $params['currencySymbol'] = getsetting('paypalcurrency', 'USD');
    $params['currencyUnits'] = getsetting('dpointspercurrencyunit', 100);
    $params['refererAward'] = getsetting('refereraward', 25);
    $params['referMinLevel'] = getsetting('referminlevel', 25);

    $params['donatorPointMessages'] = [
        [
            'section.points.messages.default', //-- Translator keys
            [ //-- Params for translator
                'currencySymbol' => $params['currencySymbol'],
                'currencyUnits' => $params['currencyUnits']
            ],
            $textDomain //-- Translator text domain
        ]
    ];
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_LODGE_POST);
$params = modulehook('page-lodge-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/lodge.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
