<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/forest.php';
require_once 'lib/http.php';
require_once 'lib/villagenav.php';

tlschema('healer');

$config = unserialize($session['user']['donationconfig']);

$return = httpget('return');
$returnline = $return > '' ? "&return=$return" : '';

page_header("Healer's Hut");

$cost = log($session['user']['level']) * (($session['user']['maxhitpoints'] - $session['user']['hitpoints']) + 10);
$result = modulehook('healmultiply', ['alterpct' => 1.0]);
$cost *= $result['alterpct'];
$cost = round($cost, 0);

$op = httpget('op');

$twig = ['cost' => $cost];

if ('' == $op)
{
    checkday();

    if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
    {
        $twig['needheal'] = true;
    }
    elseif ($session['user']['hitpoints'] == $session['user']['maxhitpoints'])
    {
        $twig['needheal'] = false;
    }
    else
    {
        $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
    }

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/healer.twig', $twig));
}
elseif ('buy' == $op)
{
    $pct = httpget('pct');
    $newcost = round($pct * $cost / 100, 0);
    $twig['newcost'] = $newcost;

    if ($session['user']['gold'] >= $newcost)
    {
        $twig['hasmoney'] = true;
        $session['user']['gold'] -= $newcost;
        debuglog('spent gold on healing', false, false, 'healing', $newcost);
        $diff = round(($session['user']['maxhitpoints'] - $session['user']['hitpoints']) * $pct / 100, 0);
        $session['user']['hitpoints'] += $diff;
        $twig['diff'] = $diff;
    }

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/healer/buy.twig', $twig));
}
elseif ('companion' == $op)
{
    $compcost = httpget('compcost');
    $twig['compcost'] = $compcost;

    if ($session['user']['gold'] < $compcost)
    {
        $twig['hasmoney'] = false;
    }
    else
    {
        $name = stripslashes(rawurldecode(httpget('name')));
        $session['user']['gold'] -= $compcost;
        $companions[$name]['hitpoints'] = $companions[$name]['maxhitpoints'];
        $twig['companionname'] = $companions[$name]['name'];
    }

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/healer/companion.twig', $twig));
}
$playerheal = false;

if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
{
    $playerheal = true;
    addnav('Potions');
    addnav('`^Complete Healing`0', "healer.php?op=buy&pct=100$returnline");

    for ($i = 90; $i > 0; $i -= 10)
    {
        addnav(['%s%% - %s gold', $i, round($cost * $i / 100, 0)], "healer.php?op=buy&pct=$i$returnline");
    }
    modulehook('potion');
}
addnav('`bHeal Companions`b');
$compheal = false;

foreach ($companions as $name => $companion)
{
    if (isset($companion['cannotbehealed']) && true == $companion['cannotbehealed'])
    {
    }
    else
    {
        $points = $companion['maxhitpoints'] - $companion['hitpoints'];

        if ($points > 0)
        {
            $compcost = round(log($session['user']['level'] + 1) * ($points + 10) * 1.33);
            addnav(['%s`0 (`^%s Gold`0)', $companion['name'], $compcost], 'healer.php?op=companion&name='.rawurlencode($name)."&compcost=$compcost$returnline");
            $compheal = true;
        }
    }
}
tlschema('nav');
addnav('`bReturn`b');

if ('' == $return)
{
    if ($playerheal || $compheal)
    {
        addnav('F?Back to the Forest', 'forest.php');
        villagenav();
    }
    else
    {
        forest(true);
    }
}
elseif ('village.php' == $return)
{
    villagenav();
}
else
{
    addnav('R?Return whence you came', $return);
}

tlschema();
output_notl('`0');
page_footer();
