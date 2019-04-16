<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/bell_rand.php';
require_once 'common.php';
require_once 'lib/substitute.php';
require_once 'lib/battle/functions.php';
require_once 'lib/battle/buffs.php';
require_once 'lib/battle/skills.php';
require_once 'lib/battle/extended.php';
require_once 'lib/buffs.php';

//just in case we're called from within a function.Yuck is this ugly.
global $badguy, $enemies, $newenemies, $session, $creatureattack, $creatureatkmod, $beta;
global $creaturedefmod, $adjustment, $defmod, $atkmod, $compdefmod, $compatkmod, $buffset, $atk, $def, $options;
global $companions, $companion, $newcompanions, $countround, $defended, $needtostopfighting, $roll, $lotgdBattleContent, $content;

tlschema('battle');

$newcompanions = [];
$lotgdBattleContent = [
    'battlestatus' => true,
    'msg' => [],
    'encounter' => [],
    'battlebars' => [],
    'battlestart' => [],
    'battlerounds' => [],
    'battleend' => []
];
$content = &$lotgdBattleContent;

$attackstack = @unserialize($session['user']['badguy']);

if (isset($attackstack['enemies']))
{
    $enemies = $attackstack['enemies'];
}

if (isset($attackstack['options']))
{
    $options = $attackstack['options'];
}

// Make the new battle script compatible with old, single enemy fights.
if (isset($attackstack['creaturename']) && $attackstack['creaturename'] > '')
{
    $safe = $attackstack;
    $enemies = [];
    $enemies[0] = $safe;
    unset($safe);
}
elseif (isset($attackstack[0]['creaturename']) && $attackstack['creaturename'] > '')
{
    $enemies = $attackstack;
}

if (! isset($options) && isset($enemies[0]['type']))
{
    $options['type'] = $enemies[0]['type'];
}

$options = prepare_fight($options);

$roundcounter = 0;
$adjustment = 1;

$count = 1;
$auto = \LotgdHttp::getQuery('auto');

if ('full' == $auto)
{
    $count = -1;
}
elseif ('five' == $auto)
{
    $count = 5;
}
elseif ('ten' == $auto)
{
    $count = 10;
}

$enemycounter = count($enemies);
$enemies = autosettarget($enemies);

$op = \LotgdHttp::getQuery('op');
$skill = \LotgdHttp::getQuery('skill');
$l = \LotgdHttp::getQuery('l');
$newtarget = \LotgdHttp::getQuery('newtarget');

if ('' != $newtarget)
{
    $op = 'newtarget';
}

if ('fight' == $op)
{
    apply_skill($skill, $l);
}
elseif ('newtarget' == $op)
{
    foreach ($enemies as $index => $badguy)
    {
        if ($index == (int) $newtarget)
        {
            if (! isset($badguy['cannotbetarget']) || false === $badguy['cannotbetarget'])
            {
                $enemies[$index]['istarget'] = 1;
            }
            else
            {
                if (is_array($badguy['cannotbetarget']))
                {
                    $lotgdBattleContent['msg'][] = substitute($msg);
                }
                else
                {
                    $msg = $badguy['cannotbetarget'];

                    if (true === $badguy['cannotbetarget'])
                    {
                        $msg = '{badguy} cannot be selected as target.';
                    }

                    $lotgdBattleContent['msg'][] = substitute_array("`5{$msg}`0`n");
                }
            }
        }
        else
        {
            $enemies[$index]['istarget'] = 0;
        }
    }
}

$victory = false;
$defeat = false;

if ($enemycounter > 0)
{
    modulehook('battle-turn-start', $enemies);
    $lotgdBattleContent['enemies'] = $enemies;

    $data = prepare_data_battlebars($enemies);
    $lotgdBattleContent['battlebars']['start'] = [
        'player' => $data['user'],
        'companions' => $data['companions'],
        'enemies' => $data['enemies'],
        'battleoptions' => $options
    ];
    unset($data);
}

suspend_buffs((('pvp' == $options['type']) ? 'allowinpvp' : false));
suspend_companions((('pvp' == $options['type']) ? 'allowinpvp' : false));

// Now that the bufflist is sane, see if we should add in the bodyguard.
$inn = (int) \LotgdHttp::getQuery('inn');

if ('pvp' == $options['type'] && 1 == $inn)
{
    apply_bodyguard($enemies[0]['bodyguardlevel']);
}

$surprised = false;
$countround = 0;

if ('run' != $op && 'fight' != $op && 'newtarget' != $op)
{
    if (count($enemies) > 1)
    {
        $surprised = true;
        $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = '`b`^YOUR ENEMIES`$ surprise you and get the first round of attack!`0´b`n`n';
    }
    else
    {
        // Let's try this instead.Biggest change is that it adds possibility of
        // being surprised to all fights.
        if (! array_key_exists('didsurprise', $options) || ! $options['didsurprise'])
        {
            // By default, surprise is 50/50
            $surprised = e_rand(0, 1) ? true : false;
            // Now, adjust for slum/thrill
            $type = \LotgdHttp::getQuery('type');

            if ('slum' == $type || 'thrill' == $type)
            {
                $num = e_rand(0, 2);
                $surprised = true;

                if ('slum' == $type && 2 != $num)
                {
                    $surprised = false;
                }

                if (('thrill' == $type || 'suicide' == $type) && 2 == $num)
                {
                    $surprised = false;
                }
            }

            if (! $surprised)
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = '`b`$Your skill allows you to get the first attack!`0´b`n`n';
            }
            else
            {
                if ('pvp' == $options['type'])
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ["`b`^%s`\$'s skill allows them to get the first round of attack!`0´b`n`n", $enemies[0]['creaturename']];
                }
                else
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`b`^%s`$ surprises you and gets the first round of attack!`0´b`n`n', $enemies[0]['creaturename']];
                }

                $op = 'run';
            }
            $options['didsurprise'] = 1;
            $options['endbattle'] = 0;
        }
    }
}
$needtostopfighting = false;

if ('newtarget' != $op)
{
    // Run through as many rounds as needed.
    do
    {
        //we need to restore and calculate here to reflect changes that happen throughout the course of multiple rounds.
        modulehook('startofround-prebuffs'); //-- For Stamina System
        restore_buff_fields();
        calculate_buff_fields();
        prepare_companions();
        $newenemies = [];
        // Run the beginning of round buffs (this also calculates all modifiers)
        foreach ($enemies as $index => $badguy)
        {
            if (false == $badguy['dead'] && $badguy['creaturehealth'] > 0)
            {
                if (! isset($badguy['alwaysattacks']) || true != $badguy['alwaysattacks'])
                {
                    $roundcounter++;
                }

                if (($roundcounter > $options['maxattacks']) && false == $badguy['istarget'])
                {
                    $newcompanions = $companions;
                }
                else
                {
                    $buffset = activate_buffs('roundstart');

                    if ($badguy['creaturehealth'] <= 0 || $session['user']['hitpoints'] <= 0)
                    {
                        $creaturedmg = 0;
                        $selfdmg = 0;

                        if ($badguy['creaturehealth'] <= 0)
                        {
                            $badguy['dead'] = true;
                            $badguy['istarget'] = false;
                            $count = 1;
                            $needtostopfighting = true;
                        }

                        if ($session['user']['hitpoints'] <= 0)
                        {
                            $count = 1;
                            $needtostopfighting = true;
                        }
                        $newenemies[$index] = $badguy;
                        $newcompanions = $companions;
                    // No break here. It would break the foreach statement.
                    }
                    else
                    {
                        $creaturedefmod = $buffset['badguydefmod'];
                        $creatureatkmod = $buffset['badguyatkmod'];
                        $atkmod = $buffset['atkmod'];
                        $defmod = $buffset['defmod'];
                        $compatkmod = $buffset['compatkmod'];
                        $compdefmod = $buffset['compdefmod'];

                        if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && $badguy['istarget'])
                        {
                            if (is_array($companions))
                            {
                                $newcompanions = [];

                                foreach ($companions as $name => $companion)
                                {
                                    if ($companion['hitpoints'] > 0)
                                    {
                                        $buffer = report_companion_move($companion, 'heal');

                                        if (false !== $buffer)
                                        {
                                            $newcompanions[$name] = $buffer;
                                            unset($buffer);
                                        }
                                        else
                                        {
                                            unset($companion, $newcompanions[$name]);
                                        }
                                    }
                                    else
                                    {
                                        $newcompanions[$name] = $companion;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $newcompanions = $companions;
                        }

                        $companions = $newcompanions;

                        if ('fight' == $op || 'run' == $op || $surprised)
                        {
                            // Grab an initial roll.
                            $roll = rolldamage();

                            if ('fight' == $op && ! $surprised)
                            {
                                $ggchancetodouble = $session['user']['dragonkills'];
                                $bgchancetodouble = $session['user']['dragonkills'];

                                if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0)
                                {
                                    $buffset = activate_buffs('offense');

                                    if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && $badguy['istarget'] && is_array($companions))
                                    {
                                        if (is_array($companions))
                                        {
                                            $newcompanions = [];

                                            foreach ($companions as $name => $companion)
                                            {
                                                if ($companion['hitpoints'] > 0)
                                                {
                                                    $buffer = report_companion_move($companion, 'magic');

                                                    if (false !== $buffer)
                                                    {
                                                        $newcompanions[$name] = $buffer;
                                                        unset($buffer);
                                                    }
                                                    else
                                                    {
                                                        unset($companion, $newcompanions[$name]);
                                                    }
                                                }
                                                else
                                                {
                                                    $newcompanions[$name] = $companion;
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $newcompanions = $companions;
                                    }

                                    $companions = $newcompanions;

                                    if ($badguy['creaturehealth'] <= 0 || $session['user']['hitpoints'] <= 0)
                                    {
                                        $creaturedmg = 0;
                                        $selfdmg = 0;

                                        if ($badguy['creaturehealth'] <= 0)
                                        {
                                            $badguy['dead'] = true;
                                            $badguy['istarget'] = false;
                                            $count = 1;
                                            $needtostopfighting = true;
                                        }
                                        $newenemies[$index] = $badguy;
                                        $newcompanions = $companions;
                                    // No break here. It would break the foreach statement.
                                    }
                                    elseif (true == $badguy['istarget'])
                                    {
                                        do
                                        {
                                            if ($badguy['creaturehealth'] <= 0 || $session['user']['hitpoints'] <= 0)
                                            {
                                                $creaturedmg = 0;
                                                $selfdmg = 0;
                                                $newenemies[$index] = $badguy;
                                                $newcompanions = $companions;
                                                $needtostopfighting = true;
                                            }
                                            else
                                            {
                                                $needtostopfighting = battle_player_attacks();
                                            }

                                            $r = mt_rand(0, 100);

                                            if ($r < $ggchancetodouble && $badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && ! $needtostopfighting)
                                            {
                                                $additionalattack = true;
                                                $ggchancetodouble -= ($r + 5);
                                                $roll = rolldamage();
                                            }
                                            else
                                            {
                                                $additionalattack = false;
                                            }
                                        } while ($additionalattack && ! $needtostopfighting);

                                        if ($needtostopfighting)
                                        {
                                            $newcompanions = $companions;
                                        }
                                    }
                                }
                            }
                            elseif ('run' == $op && ! $surprised)
                            {
                                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`4You are too busy trying to run away like a cowardly dog to try to fight `^%s`4.`n', $badguy['creaturename']];
                            }

                            //Need to insert this here because of auto-fighting!
                            if ('newtarget' != $op)
                            {
                                $op = 'fight';
                            }

                            // We need to check both user health and creature health. Otherwise
                            // the user can win a battle by a RIPOSTE after he has gone <= 0 HP.
                            //-- Gunnar Kreitz
                            if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && $roundcounter <= $options['maxattacks'])
                            {
                                $buffset = activate_buffs('defense');

                                do
                                {
                                    $defended = false;
                                    $needtostopfighting = battle_badguy_attacks();
                                    $r = mt_rand(0, 100);

                                    if (! isset($bgchancetodouble))
                                    {
                                        $bgchancetodouble = 0;
                                    }

                                    if ($r < $bgchancetodouble && $badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && ! $needtostopfighting)
                                    {
                                        $additionalattack = true;
                                        $bgchancetodouble -= ($r + 5);
                                        $roll = rolldamage();
                                    }
                                    else
                                    {
                                        $additionalattack = false;
                                    }
                                } while ($additionalattack && ! $defended);
                            }

                            $companions = $newcompanions;

                            if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && $badguy['istarget'])
                            {
                                if (is_array($companions))
                                {
                                    foreach ($companions as $name => $companion)
                                    {
                                        if ($companion['hitpoints'] > 0)
                                        {
                                            $buffer = report_companion_move($companion, 'fight');

                                            if (false !== $buffer)
                                            {
                                                $newcompanions[$name] = $buffer;
                                                unset($buffer);
                                            }
                                            else
                                            {
                                                unset($companion, $newcompanions[$name]);
                                            }
                                        }
                                        else
                                        {
                                            $newcompanions[$name] = $companion;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $newcompanions = $companions;
                            }
                        }
                        else
                        {
                            $newcompanions = $companions;
                        }

                        if (false == $badguy['dead'] && isset($badguy['creatureaiscript']) && $badguy['creatureaiscript'] > '')
                        {
                            global $unsetme;

                            execute_ai_script($badguy['creatureaiscript']);
                        }
                    }
                }
            }
            else
            {
                $newcompanions = $companions;
            }
            // Copy the companions back so in the next round (multiple rounds) they can be used again.
            // We will also delete the now old set of companions. Just in case.
            $companions = $newcompanions;
            unset($newcompanions);

            // If any A.I. script wants the current enemy to be deleted completely, we will obey.
            // For multiple rounds/multiple A.I. scripts we will although unset this order.
            if (isset($unsetme) && true === $unsetme)
            {
                $unsetme = false;
                unset($unsetme);
            }
            else
            {
                $newenemies[$index] = $badguy;
            }

            if ($surprised || 'run' == $op || 'fight' == $op || 'newtarget' == $op)
            {
                $badguy = modulehook('endofround', $badguy);
            } //-- For Stamina System
        }
        expire_buffs();
        $creaturedmg = 0;
        $selfdmg = 0;

        if (count($newenemies) > 0)
        {
            $verynewenemies = [];
            $alive = 0;
            $fleeable = 0;
            $leaderisdead = false;

            foreach ($newenemies as $index => $badguy)
            {
                if (true == $badguy['dead'] || $badguy['creaturehealth'] <= 0)
                {
                    if (isset($badguy['essentialleader']) && true == $badguy['essentialleader'])
                    {
                        $defeat = false;
                        $victory = true;
                        $needtostopfighting = true;
                        $leaderisdead = true;
                    }
                    $badguy['istarget'] = false;
                    // We'll either add the experience right away or store it in a seperate array.
                    // If through any script enemies are added during the fight, the amount of
                    // experience would stay the same
                    // We'll also check if the user is actually alive. If we didn't, we would hand out
                    // experience for graveyard fights.
                    if (true == getsetting('instantexp', false) && $session['user']['alive'] && 'pvp' != $options['type'] && 'train' != $options['type'])
                    {
                        if (! isset($badguy['expgained']) || false == $badguy['expgained'])
                        {
                            $expgain = round($badguy['creatureexp'] / count($newenemies));

                            if (! isset($badguy['creatureexp']))
                            {
                                $badguy['creatureexp'] = 0;
                            }
                            $session['user']['experience'] += $expgain;
                            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`#You receive `^%s`# experience!`n`0', $expgain];
                            $options['experience'][$index] = $badguy['creatureexp'];
                            $options['experiencegained'][$index] = $expgain;
                            $badguy['expgained'] = true;
                        }
                    }
                    else
                    {
                        $options['experience'][$index] = $badguy['creatureexp'];
                    }
                }
                else
                {
                    $alive++;

                    if (isset($badguy['fleesifalone']) && true == $badguy['fleesifalone'])
                    {
                        $fleeable++;
                    }

                    if ($session['user']['hitpoints'] <= 0)
                    {
                        $defeat = true;
                        $victory = false;

                        break;
                    }
                    elseif (! $leaderisdead)
                    {
                        $defeat = false;
                        $victory = false;
                    }
                }

                $verynewenemies[$index] = $badguy;
            }
            $enemiesflown = false;

            if ($alive == $fleeable && $session['user']['hitpoints'] > 0)
            {
                $defeat = false;
                $victory = true;
                $enemiesflown = true;
                $needtostopfighting = true;
            }
        }

        if (0 == $alive)
        {
            $defeat = false;
            $victory = true;
            $needtostopfighting = true;
        }

        if (-1 != $count)
        {
            $count--;
        }

        if ($needtostopfighting)
        {
            $count = 0;
        }

        if ($enemiesflown)
        {
            foreach ($newenemies as $index => $badguy)
            {
                if (isset($badguy['fleesifalone']) && true == $badguy['fleesifalone'])
                {
                    if (is_array($badguy['fleesifalone']))
                    {
                        $msg = substitute($badguy['fleesifalone']);
                    }
                    else
                    {
                        if (true === $badguy['fleesifalone'])
                        {
                            $msg = '{badguy} flees in panic.';
                        }
                        else
                        {
                            $msg = $badguy['fleesifalone'];
                        }

                        $msg = substitute_array("`5{$msg}`0`n");
                    }

                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = $msg;
                }
                else
                {
                    $newenemies[$index] = $badguy;
                }
            }
        }
        elseif ($leaderisdead)
        {
            if (is_array($badguy['essentialleader']))
            {
                $msg = substitute($badguy['essentialleader']);
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = $msg;
            }
            else
            {
                if (true === $badguy['essentialleader'])
                {
                    $msg = 'All other other enemies flee in panic as `^{badguy}`5 falls to the ground.';
                }
                else
                {
                    $msg = $badguy['essentialleader'];
                }
                $msg = substitute_array("`5{$msg}`0`n");
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = $msg;
            }
        }

        if (is_array($newenemies))
        {
            $enemies = $newenemies;
        }

        $roundcounter = 0;
        $countround++;
    } while ($count > 0 || -1 == $count);

    $newenemies = $enemies;
}
else
{
    $newenemies = $enemies;
}

$newenemies = autosettarget($newenemies);

$badguy = modulehook('endofpage', $badguy);

if ($session['user']['hitpoints'] <= 0)
{
    $session['user']['hitpoints'] = 0;
    $victory = false;
    $defeat = true;
}

//-- Any data for personalize results
$battleDefeatWhere = $battleDefeatWhere ?? 'in the forest';
//-- Use for create a news, set to false for not create news
$battleInForest = $battleInForest ?? true;
//-- Indicating if is a Forest (true) or Graveyard (false)
$battleDefeatLostGold = $battleDefeatLostGold ?? true;
//-- Indicating if lost gold when lost in battle
$battleDefeatLostExp = $battleDefeatLostExp ?? true;
//-- Indicating if lost exp when lost in battle
$battleDefeatCanDie = $battleDefeatCanDie ?? true;
//-- Indicating if die when lost in battle
$battleDenyFlawless = $battleDenyFlawless ?? false;
//-- Deny flawlees for perfect battle
$battleShowResult = $battleShowResult ?? true;
//-- Show result of battle. If no need any extra modification of result no need change this
$battleProcessVictoryDefeat = $battleProcessVictoryDefeat ?? true;

//-- Process victory or defeat functions when the battle is over
if ($victory || $defeat)
{
    $options['endbattle'] = 1;
    // expire any buffs which cannot persist across fights and
    // unsuspend any suspended buffs
    expire_buffs_afterbattle();
    //unsuspend any suspended buffs
    unsuspend_buffs((('pvp' == $options['type']) ? 'allowinpvp' : false));

    if ($session['user']['alive'])
    {
        unsuspend_companions((('pvp' == $options['type']) ? 'allowinpvp' : false));
    }

    foreach ($companions as $index => $companion)
    {
        if (isset($companion['expireafterfight']) && $companion['expireafterfight'])
        {
            $lotgdBattleContent['battleend'][] = $companion['dyingtext'];
            unset($companions[$index]);
        }
    }

    if (is_array($newenemies))
    {
        foreach ($newenemies as $index => $badguy)
        {
            //-- Not use this hooks, better use battle-victory-end and battle-defeat-end
            //-- This other hooks have an array with all enemies and can add a extra information

            //-- This hooks triger for each badguy and maybe saturate server
            //-- Legacy support
            if ($victory)
            {
                $badguy = modulehook('battle-victory', $badguy);
            }

            if ($defeat)
            {
                $badguy = modulehook('battle-defeat', $badguy);
            }
        }
    }

    if ($victory)
    {
        $result = modulehook('battle-victory-end', ['enemies' => $newenemies, 'options' => $options, 'messages' => []]);
        $newenemies = $result['enemies'];

        $lotgdBattleContent['battleend'] = $lotgdBattleContent['battleend'] + $result['messages'];

        if ($battleProcessVictoryDefeat)
        {
            battlevictory($newenemies, ($options['denyflawless'] ?? $battleDenyFlawless), $battleInForest);
        }
    }
    elseif ($defeat)
    {
        $result = modulehook('battle-defeat-end', ['enemies' => $newenemies, 'options' => $options, 'messages' => []]);
        $newenemies = $result['enemies'];

        $lotgdBattleContent['battleend'] = $lotgdBattleContent['battleend'] + $result['messages'];

        if ($battleProcessVictoryDefeat)
        {
            battledefeat($newenemies, $battleDefeatWhere, $battleInForest, $battleDefeatCanDie, $battleDefeatLostExp, $battleDefeatLostGold);
        }
    }
}

if ($enemycounter > 0)
{
    modulehook('battle-turn-end', $newenemies);
    $lotgdBattleContent['enemies'] = $newenemies;

    $data = prepare_data_battlebars($newenemies);
    $lotgdBattleContent['battlebars']['end'] = [
        'player' => $data['user'],
        'companions' => $data['companions'],
        'enemies' => $data['enemies'],
        'battleoptions' => $options
    ];
    unset($data);
}

$attackstack = ['enemies' => $newenemies, 'options' => $options];
$session['user']['badguy'] = createstring($attackstack);
$session['user']['companions'] = createstring($companions);
$session['user']['battle']['options'] = $options;

if ($battleShowResult)
{
    battleshowresults($lotgdBattleContent);
}

tlschema();

//-- If battle end in defeat, break page after show content
if ($defeat && $battleShowResult)
{
    page_footer();
}
