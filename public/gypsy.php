<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

// Don't hook on to this text for your standard modules please, use "gypsy" instead.
// This hook is specifically to allow modules that do other gypsys to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_gypsy', 'textDomainNavigation' => 'navigation_gypsy']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_GYPSY_PRE);
$result = modulehook('gypsy-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request $request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

//-- Init page
\LotgdResponse::pageStart('title.default', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'cost' =>  $session['user']['level'] * 20
];

$request->attributes->set('params', $params);

$op = (string) $request->query->get('op');

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.navigation');
if ('pay' == $op)
{
    $method = 'pay';

    \LotgdNavigation::villageNav();
}
elseif ('talk' == $op)
{
    \LotgdResponse::pageTitle('title.talk', [], $textDomain);

    $method = 'talk';

    \LotgdNavigation::addNav('nav.snap', 'gypsy.php');
}
else
{
    $method = 'index';

    \LotgdNavigation::addHeader('category.seance');
    \LotgdNavigation::addNav('nav.pay', 'gypsy.php?op=pay', [
            'params' => [
                'cost' => $params['cost']
            ]
        ]
    );

    if (($session['user']['superuser'] & SU_EDIT_COMMENTS) !== 0)
    {
        \LotgdNavigation::addNav('nav.superuser', 'gypsy.php?op=talk');
    }

    \LotgdNavigation::addHeader('category.other');
    \LotgdNavigation::addNav('nav.forget', 'village.php');
}

\LotgdResponse::callController(Lotgd\Core\Controller\GypsyController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
