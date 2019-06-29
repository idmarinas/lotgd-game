<?php

require_once 'common.php';

checkday();

tlschema('shades');

//-- You can only stay in the shades if you're dead.
if ($session['user']['alive'])
{
    return redirect('village.php');
}

// Don't hook on to this text for your standard modules please, use "shades" instead.
// This hook is specifically to allow modules that do other shades to create ambience.
$result = modulehook('shades-text-domain', ['textDomain' => 'page-shades', 'textDomainNavigation' => 'navigation-shades']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$params = [
    'textDomain' => $textDomain
];

page_header('title', [], $textDomain);

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
\LotgdNavigation::addNav('nav.faq', 'petition.php?op=faq', [
    'attributes' => [
        'data-force' => 'true',
        'onclick' => 'Lotgd.embed(this)'
    ]
]);

//-- Superuser menu
\LotgdNavigation::superuser();

tlschema();

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-shades-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/shades.twig', $params));

page_footer();
