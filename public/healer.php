<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/forest.php';

tlschema('healer');

// Don't hook on to this text for your standard modules please, use "healer" instead.
// This hook is specifically to allow modules that do other healers to create ambience.
$result = modulehook('healer-text-domain', ['textDomain' => 'page-healer', 'textDomainNavigation' => 'navigation-healer']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

page_header('title', [], $textDomain);

//-- Calculate cost for healing
$cost = log($session['user']['level']) * (($session['user']['maxhitpoints'] - $session['user']['hitpoints']) + 10);
$result = modulehook('healmultiply', ['alterpct' => 1.0, 'cost' => $cost]);
$cost = round($result['alterpct'] * $result['cost'], 0);

$params = [
    'textDomain' => $textDomain,
    'healCost' => $cost
];

$op = (string) \LotgdHttp::getQuery('op');
$return = (string) \LotgdHttp::getQuery('return');

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

if ('' == $op)
{
    checkday();

    $params['tpl'] = 'default';
    $params['needHeal'] = false;

    if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
    {
        $params['needHeal'] = true;
    }
    elseif ($session['user']['hitpoints'] == $session['user']['maxhitpoints'])
    {
        $params['needHeal'] = 0;
    }

    if (false === $params['needHeal'])
    {
        $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
    }
}
elseif ('buy' == $op)
{
    $pct = (int) \LotgdHttp::getQuery('pct');
    $newcost = round($pct * $cost / 100, 0);

    $params['tpl'] = 'buy';
    $params['newHealCost'] = $newcost;
    $params['canHeal'] = false;

    if ($session['user']['gold'] >= $newcost)
    {
        $diff = round(($session['user']['maxhitpoints'] - $session['user']['hitpoints']) * $pct / 100, 0);

        $params['canHeal'] = true;
        $params['healHealed'] = $diff;

        $session['user']['gold'] -= $newcost;
        $session['user']['hitpoints'] += $diff;

        debuglog('spent gold on healing', false, false, 'healing', $newcost);
    }
}
elseif ('companion' == $op)
{
    $compcost = (int) \LotgdHttp::getQuery('compcost');

    $params['tpl'] = 'companion';
    $params['canHeal'] = false;
    $params['newHealCost'] = $compcost;

    if ($session['user']['gold'] >= $compcost)
    {
        $params['canHeal'] = true;

        $name = stripslashes(rawurldecode(\LotgdHttp::getQuery('name')));

        $session['user']['gold'] -= $compcost;
        $companions[$name]['hitpoints'] = $companions[$name]['maxhitpoints'];

        $params['companionName'] = $companions[$name]['name'];
    }
}

if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
{
    \LotgdNavigation::addHeader('category.heal.potion');
    \LotgdNavigation::addNav('nav.heal.complete', "healer.php?op=buy&pct=100&return={$return}");

    for ($i = 90; $i > 0; $i -= 10)
    {
        \LotgdNavigation::addNav('nav.heal.percent', "healer.php?op=buy&pct={$i}&return={$return}", [
            'params' => [
                'percent' => $i / 100,
                'cost' => round($cost * ($i / 100), 0)
            ]
        ]);
    }
    modulehook('potion');
}
\LotgdNavigation::addHeader('category.heal.companion');

foreach ($companions as $name => $companion)
{
    if ($companion['cannotbehealed'] ?? false)
    {
        continue;
    }

    $points = $companion['maxhitpoints'] - $companion['hitpoints'];

    if ($points > 0)
    {
        $name = rawurlencode($name);
        $compcost = round(log($session['user']['level'] + 1) * ($points + 10) * 1.33);
        \LotgdNavigation::addNav('nav.heal.companion', "healer.php?op=companion&name={$name}&compcost={$compcost}&return={$return}", [
            'params' => [
                'companionName' => $companion['name'],
                'cost' => $compcost
            ]
        ]);
    }
}

\LotgdNavigation::addHeader('category.return');

if ('' == $return)
{
    \LotgdNavigation::addNav('nav.return.forest', 'forest.php');
    \LotgdNavigation::villageNav();

    if ($session['user']['hitpoints'] >= $session['user']['maxhitpoints'])
    {
        \LotgdNavigation::addHeader('category.fight');
        \LotgdNavigation::addNav('nav.fight.search', 'forest.php?op=search');
        ($session['user']['level'] > 1) && \LotgdNavigation::addNav('nav.slum', 'forest.php?op=search&type=slum');
        \LotgdNavigation::addNav('nav.fight.thrill', 'forest.php?op=search&type=thrill');
        (getsetting('suicide', 0) && getsetting('suicidedk', 10) <= $session['user']['dragonkills']) && \LotgdNavigation::addNav('nav.fight.suicide', 'forest.php?op=search&type=suicide');
    }
}
elseif ('village.php' == $return)
{
    \LotgdNavigation::villageNav();
}
else
{
    \LotgdNavigation::addNav('nav.return.return', $return);
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-healer-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/healer.twig', $params));

page_footer();
