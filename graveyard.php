<?php

// addnews ready.
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/buffs.php';
require_once 'lib/events.php';

global $deathoverlord;

tlschema('graveyard');

page_header('The Graveyard');
$skipgraveyardtext = handle_event('graveyard');
$deathoverlord = getsetting('deathoverlord', '`$Ramius`0');

if (! $skipgraveyardtext)
{
    if ($session['user']['alive'])
    {
        redirect('village.php');
    }

    checkday();
}

$battle = false;
strip_all_buffs();
$max = $session['user']['level'] * 10 + $session['user']['dragonkills'] * 2 + 50;
$favortoheal = modulehook('favortoheal', ['favor' => round(10 * ($max - $session['user']['soulpoints']) / $max)]);

$favortoheal = (int) $favortoheal['favor'];

$op = httpget('op');

switch ($op)
{
    case 'search':
        require_once 'lib/graveyard/case_battle_search.php';
    break;
    case 'run':
        if (1 == e_rand(0, 2))
        {
            output('`$%s`) curses you for your cowardice.`n`n', $deathoverlord);
            $favor = 5 + e_rand(0, $session['user']['level']);

            if ($favor > $session['user']['deathpower'])
            {
                $favor = $session['user']['deathpower'];
            }

            if ($favor > 0)
            {
                output('`)You have `$LOST `^%s`) favor with `$%s`).', $favor, $deathoverlord);
                $session['user']['deathpower'] -= $favor;
            }
            tlschema('nav');
            addnav('G?Return to the Graveyard', 'graveyard.php');
            tlschema();
        }
        else
        {
            output('`)As you try to flee, you are summoned back to the fight!`n`n');
            $battle = true;
        }
    break;
    case 'fight':
        $battle = true;
    break;
}

if ($battle)
{
    //-- Any data for personalize results
    $battleDefeatWhere = 'in the graveyard';
    $battleInForest = false;
    $battleDefeatLostGold = false;
    $battleDefeatLostExp = false;
    $battleDefeatCanDie = false;

    //make some adjustments to the user to put them on mostly even ground
    //with the undead guy.
    $originalhitpoints = $session['user']['hitpoints'];
    $session['user']['hitpoints'] = $session['user']['soulpoints'];
    $originalattack = $session['user']['attack'];
    $originaldefense = $session['user']['defense'];

    require_once 'battle.php';

    //reverse those adjustments, battle calculations are over.
    $session['user']['attack'] = $originalattack;
    $session['user']['defense'] = $originaldefense;
    $session['user']['soulpoints'] = $session['user']['hitpoints'];
    $session['user']['hitpoints'] = $originalhitpoints;

    if ($victory)
    {
        $op = '';
        httpset('op', '');
        $skipgraveyardtext = true;
    }
    else
    {
        require_once 'lib/fightnav.php';

        fightnav(false, true, 'graveyard.php');
    }
}

modulehook('deathoverlord', []);

switch ($op)
{
    case 'search': case 'run': case 'fight':
        break;
    case 'enter':
        require_once 'lib/graveyard/case_enter.php';
        break;
    case 'restore':
        require_once 'lib/graveyard/case_restore.php';
        break;
    case 'resurrection':
        require_once 'lib/graveyard/case_resurrection.php';
        break;
    case 'question':
        require_once 'lib/graveyard/case_question.php';
        break;
    case 'haunt':
        require_once 'lib/graveyard/case_haunt.php';
        break;
    case 'haunt2':
        require_once 'lib/graveyard/case_haunt2.php';
        break;
    case 'haunt3':
        require_once 'lib/graveyard/case_haunt3.php';
        break;
    default:
        require_once 'lib/graveyard/case_default.php';
        break;
}

page_footer();
