<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/sanitize.php';
require_once 'lib/buffs.php';

modulehook('newday-intercept', []);

$resurrection = (string) \LotgdHttp::getQuery('resurrection');
$resline = ('true' == $resurrection) ? '&resurrection=true' : '';

/***************
 **  SETTINGS **
 ***************/
$turnsperday = getsetting('turns', 10);
$maxinterest = ((float) getsetting('maxinterest', 10) / 100) + 1; //-- 1.1
$mininterest = ((float) getsetting('mininterest', 1) / 100) + 1; //-- 1.1
$dailypvpfights = getsetting('pvpday', 3);
/******************
 ** End Settings **
 ******************/

// Don't hook on to this text for your standard modules please, use "newday" instead.
// This hook is specifically to allow modules that do other newdays to create ambience.
$result = modulehook('newday-text-domain', ['textDomain' => 'page-newday', 'textDomainNavigation' => 'navigation-newday']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$params = [
    'textDomain' => $textDomain,
    'includeTemplatesPre' => [], //-- Templates that are in top of content (but below of title)
    'includeTemplatesPost' => [] //-- Templates that are in bottom of content
];

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.continue');

$dk = \LotgdHttp::getQuery('dk');

if ((count($session['user']['dragonpoints']) < $session['user']['dragonkills']) && '' != $dk)
{
    array_push($session['user']['dragonpoints'], $dk);

    switch ($dk)
    {
        case 'str':
            $session['user']['strength']++;
            break;
        case 'dex':
            $session['user']['dexterity']++;
            break;
        case 'con':
            $session['user']['constitution']++;
            break;
        case 'int':
            $session['user']['intelligence']++;
            break;
        case 'wis':
            $session['user']['wisdom']++;
            break;
    }
}

$labels = [
    'General Stuff,title',
        'ff' => 'Forest Fights + 1',
    'Attributes,title',
        'str' => 'Strength +1',
        'dex' => 'Dexterity +1',
        'con' => 'Constitution +1',
        'int' => 'Intelligence +1',
        'wis' => 'Wisdom +1',
    'unknown' => 'Unknown Spends (contact an admin to investigate!)',
];

/**
 * Use modulehook dkpointlabels to activate/desactivate labels or add more labels.
 */
$canbuy = [
    'ff' => 1,
    'str' => 1,
    'dex' => 1,
    'con' => 1,
    'int' => 1,
    'wis' => 1,
    'unknown' => 0,
];

if (is_module_active('staminasystem'))
{
    $canbuy['ff'] = 0;
}
$retargs = modulehook('dkpointlabels', ['desc' => $labels, 'buy' => $canbuy]);
$labels = $retargs['desc'];
$canbuy = $retargs['buy'];

$pdk = \LotgdHttp::getQuery('pdk');

$dp = count($session['user']['dragonpoints']);
$dkills = $session['user']['dragonkills'];

if (1 == $pdk)
{
    require_once 'lib/newday/dp_recalc.php';
}

if ($dp < $dkills)
{
    require_once 'lib/newday/dragonpointspend.php';
}
elseif (! $session['user']['race'] || RACE_UNKNOWN == $session['user']['race'])
{
    require_once 'lib/newday/setrace.php';
}
elseif ('' == $session['user']['specialty'])
{
    require_once 'lib/newday/setspecialty.php';
}
else
{
    require_once 'lib/newday/newday.php';
}

$params['turnsPerDay'] = $turnsperday;

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-newday-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/newday.twig', $params));

page_footer();
