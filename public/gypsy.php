<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/commentary.php';
require_once 'lib/villagenav.php';

// Don't hook on to this text for your standard modules please, use "gypsy" instead.
// This hook is specifically to allow modules that do other gypsys to create ambience.
$result = modulehook('gypsy-text-domain', ['textDomain' => 'page-gypsy', 'textDomainNavigation' => 'navigation-gypsy']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

page_header('title.default', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'cost' =>  $session['user']['level'] * 20
];

$op = (string) \LotgdHttp::getQuery('op');

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
    page_header('title.talk', [], $textDomain);

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

    modulehook('gypsy');
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-gypsy-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/gypsy.twig', $params));

page_footer();
