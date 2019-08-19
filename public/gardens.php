<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/events.php';

tlschema('gardens');

// Don't hook on to this text for your standard modules please, use "gardens" instead.
// This hook is specifically to allow modules that do other gardenss to create ambience.
$result = modulehook('gardens-text-domain', ['textDomain' => 'page-gardens', 'textDomainNavigation' => 'navigation-gardens']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

page_header('title', [], $textDomain);

$skipgardendesc = handle_event('gardens');

$params = [
    'textDomain' => $textDomain,
    'showGardenDesc' => ! $skipgardendesc,
    'includeTemplatesPre' => [],
    'includeTemplatesPost' => []
];

$op = (string) \LotgdHttp::getQuery('op');
$com = \LotgdHttp::getQuery('commentPage');
$commenting = \LotgdHttp::getQuery('commenting');
$comment = \LotgdHttp::getPost('comment');

// Don't give people a chance at a special event if they are just browsing
// the commentary (or talking) or dealing with any of the hooks in the village.
if (! $op && '' == $com && ! $comment && ! $refresh && ! $commenting && 0 != module_events('gardens', getsetting('gardenchance', 0)))
{
    if (\LotgdNavigation::checkNavs())
    {
        page_footer();
    }
    else
    {
        // Reset the special for good.
        $session['user']['specialinc'] = '';
        $session['user']['specialmisc'] = '';
        $skipgardendesc = true;
        $params['showGardenDesc'] = ! $skipgardendesc;
        $op = '';
        \LotgdHttp::setQuery('op', '');
    }
}

if (! $skipgardendesc)
{
    checkday();
}

\LotgdNavigation::villageNav();
modulehook('gardens', []);

//-- This is only for params not use for other purpose
$params = modulehook('page-gardens-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/gardens.twig', $params));

module_display_events('gardens', 'gardens.php');

page_footer();
