<?php

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

checkday();

//-- You can only stay in the shades if you're dead.
if ($session['user']['alive'])
{
    redirect('village.php');
}

// Don't hook on to this text for your standard modules please, use "shades" instead.
// This hook is specifically to allow modules that do other shades to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_shades', 'textDomainNavigation' => 'navigation_shades']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_SHADES_PRE);
$result = modulehook('shades-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);

$params = [
    'textDomain' => $textDomain,
    'includeTemplatesPre' => [], //-- Templates that are in top of content (but below of title)
    'includeTemplatesPost' => [] //-- Templates that are in bottom of content
];

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.logout');
\LotgdNavigation::addNav('nav.logout', 'login.php?op=logout');

\LotgdNavigation::addHeader('category.places');
\LotgdNavigation::addNav('nav.graveyard', 'graveyard.php');
\LotgdNavigation::addNav('nav.news', 'news.php');

// the mute module blocks players from speaking until they
// read the FAQs, and if they first try to speak when dead
// there is no way for them to unmute themselves without this link.
\LotgdNavigation::addHeader('category.other');
\LotgdNavigation::addNav('nav.faq', '#', [
    'attributes' => [
        'id' => 'shades-petition-faq',
        'onclick' => "JaxonLotgd.Ajax.Core.Petition.faq(); $(this).addClass('disabled')"
    ]
]);

//-- Superuser menu
\LotgdNavigation::superuser();

$request->attributes->set('params', $params);

\Lotgdresponse::callController(Lotgd\Core\Controller\ShadesController::class);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
