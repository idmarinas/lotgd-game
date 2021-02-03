<?php

require_once 'common.php';

checkday();

//-- You can only stay in the shades if you're dead.
if ($session['user']['alive'])
{
    return redirect('village.php');
}

// Don't hook on to this text for your standard modules please, use "shades" instead.
// This hook is specifically to allow modules that do other shades to create ambience.
$result = modulehook('shades-text-domain', ['textDomain' => 'page_shades', 'textDomainNavigation' => 'navigation_shades']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

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

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-shades-tpl-params', $params);
\LotgdResponse::pageAddContent(\LotgdTheme::render('pages/shades.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
