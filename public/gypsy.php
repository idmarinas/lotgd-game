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

//-- Init page
\LotgdResponse::pageStart('title.default', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'cost' =>  $session['user']['level'] * 20
];

$op = (string) \LotgdRequest::getQuery('op');

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.navigation');
if ('pay' == $op)
{
    $params['tpl'] = 'pay';

    if ($session['user']['gold'] >= $params['cost'])
    { // Gunnar Kreitz
        $session['user']['gold'] -= $params['cost'];

        debuglog("spent {$params['cost']} gold to speak to the dead");

        return redirect('gypsy.php?op=talk');
    }

    \LotgdNavigation::villageNav();
}
elseif ('talk' == $op)
{
    \LotgdResponse::pageTitle('title.talk', [], $textDomain);

    $params['tpl'] = 'talk';

    \LotgdNavigation::addNav('nav.snap', 'gypsy.php');
}
else
{
    checkday();

    $params['tpl'] = 'default';

    \LotgdNavigation::addHeader('category.seance');
    \LotgdNavigation::addNav('nav.pay', 'gypsy.php?op=pay', [
            'params' => [
                'cost' => $params['cost']
            ]
        ]
    );

    if ($session['user']['superuser'] & SU_EDIT_COMMENTS)
    {
        \LotgdNavigation::addNav('nav.superuser', 'gypsy.php?op=talk');
    }

    \LotgdNavigation::addHeader('category.other');
    \LotgdNavigation::addNav('nav.forget', 'village.php');

    \LotgdEventDispatcher::dispatch(new GenericEvent(), Events::PAGE_GYPSY);
    modulehook('gypsy');
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_GYPSY_POST);
$params = modulehook('page-gypsy-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/gypsy.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
