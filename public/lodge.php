<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/names.php';

tlschema('lodge');

// Don't hook on to this text for your standard modules please, use "lodge" instead.
// This hook is specifically to allow modules that do other lodges to create ambience.
$result = modulehook('lodge-text-domain', ['textDomain' => 'page-lodge', 'textDomainNavigation' => 'navigation-lodge']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$op = (string) \LotgdHttp::getQuery('op');

if ('' == $op)
{
    checkday();
}

$pointsavailable = max(0, $session['user']['donation'] - $session['user']['donationspent']);
//-- Have access to Lodge
$entry = ($session['user']['donation'] > 0) || ($session['user']['superuser'] & SU_EDIT_COMMENTS);

page_header('title', [], $textDomain);

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
$params = modulehook('page-lodge-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/lodge.twig', $params));

page_footer();
