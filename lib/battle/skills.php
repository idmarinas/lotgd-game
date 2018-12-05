<?php

// translator ready
// addnews ready
// mail ready
require_once 'lib/bell_rand.php';
require_once 'lib/e_rand.php';
require_once 'lib/buffs.php';

function rolldamage()
{
    global $badguy, $session, $creatureattack, $creatureatkmod, $adjustment;
    global $creaturedefmod, $defmod, $atkmod, $buffset, $atk, $def, $options;

    if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0)
    {
        $adjustedcreaturedefense = $badguy['creaturedefense'];

        if ('pvp' != $options['type'])
        {
            $adjustedcreaturedefense = ($creaturedefmod * $badguy['creaturedefense'] / ($adjustment * $adjustment));
        }

        $creatureattack = $badguy['creatureattack'] * $creatureatkmod;
        $adjustedselfdefense = (get_player_defense() * $adjustment * $defmod);

        /*
        debug("Base creature defense: " . $badguy['creaturedefense']);
        debug("Creature defense mod: $creaturedefmod");
        debug("Adjustment: $adjustment");
        debug("Adjusted creature defense: $adjustedcreaturedefense");
        debug("Adjusted creature attack: $creatureattack");
        debug("Adjusted self defense: $adjustedselfdefense");
        */
        if (! isset($badguy['physicalresistance']))
        {
            $badguy['physicalresistance'] = 0;
        }
        $powerattack = (int) getsetting('forestpowerattackchance', 10);
        $powerattackmulti = (float) getsetting('forestpowerattackmulti', 3);

        while (! isset($creaturedmg) || ! isset($selfdmg) || 0 == $creaturedmg && 0 == $selfdmg)
        {
            $atk = get_player_attack() * $atkmod;

            if (1 == mt_rand(1, 20) && 'pvp' != $options['type'])
            {
                $atk *= 3;
            }
            /*
            debug("Attack score: $atk");
            */

            $patkroll = bell_rand(0, $atk);
            /*
            debug("Player Attack roll: $patkroll");
            */

            // Set up for crit detection
            $atk = $patkroll;
            $catkroll = bell_rand(0, $adjustedcreaturedefense);
            /*
            debug("Creature defense roll: $catkroll");
            */

            $creaturedmg = 0 - (int) ($catkroll - $patkroll);

            if ($creaturedmg < 0)
            {
                $creaturedmg = (int) ($creaturedmg / 2);
                $creaturedmg = round($buffset['badguydmgmod'] * $creaturedmg, 0);
                $creaturedmg = min(0, round($creaturedmg - $badguy['physicalresistance']));
            }

            if ($creaturedmg > 0)
            {
                $creaturedmg = round($buffset['dmgmod'] * $creaturedmg, 0);
                $creaturedmg = max(0, round($creaturedmg - $badguy['physicalresistance']));
            }
            $pdefroll = bell_rand(0, $adjustedselfdefense);
            $catkroll = bell_rand(0, $creatureattack);

            if (0 != $powerattack && 'pvp' != $options['type'] && 1 == e_rand(1, $powerattack))
            {
                $catkroll *= $powerattackmulti;
            }

            /*
               debug("Creature attack roll: $catkroll");
               debug("Player defense roll: $pdefroll");
             */
            $selfdmg = 0 - (int) ($pdefroll - $catkroll);

            if ($selfdmg < 0)
            {
                $selfdmg = (int) ($selfdmg / 2);
                $selfdmg = round($selfdmg * $buffset['dmgmod'], 0);
                $selfdmg = min(0, round($selfdmg - ((int) get_player_physical_resistance()), 0));
            }

            if ($selfdmg > 0)
            {
                $selfdmg = round($selfdmg * $buffset['badguydmgmod'], 0);
                $selfdmg = max(0, round($selfdmg - ((int) get_player_physical_resistance()), 0));
            }
        }
    }
    else
    {
        $creaturedmg = 0;
        $selfdmg = 0;
    }
    // Handle god mode's invulnerability
    if ($buffset['invulnerable'])
    {
        $creaturedmg = abs($creaturedmg);
        $selfdmg = -abs($selfdmg);
    }

    return ['creaturedmg' => (isset($creaturedmg) ? $creaturedmg : 0), 'selfdmg' => (isset($selfdmg) ? $selfdmg : 0)];
}

function report_power_move($crit, $dmg)
{
    global $session, $countround, $lotgdBattleContent;

    $uatk = get_player_attack();

    if ($crit > $uatk)
    {
        $power = 0;

        if ($crit > $uatk * 4)
        {
            $msg = '`&`bYou execute a `%MEGA`& power move!!!´b`n';
            $power = 1;
        }
        elseif ($crit > $uatk * 3)
        {
            $msg = '`&`bYou execute a `^DOUBLE`& power move!!!´b`n';
            $power = 1;
        }
        elseif ($crit > $uatk * 2)
        {
            $msg = '`&`bYou execute a power move!!!´b`0`n';
            $power = 1;
        }
        elseif ($crit > ($uatk * 1.5))
        {
            $msg = '`7`bYou execute a minor power move!´b`0`n';
            $power = 1;
        }

        if ($power)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;

            $dmg += e_rand($crit / 4, $crit / 2);
            $dmg = max($dmg, 1);
        }
    }

    return $dmg;
}

function suspend_buffs($susp = false, $msg = false)
{
    global $session, $badguy, $countround, $lotgdBattleContent;

    $suspendnotify = 0;
    reset($session['bufflist']);

    foreach ($session['bufflist'] as $key => $buff)
    {
        if (array_key_exists('suspended', $buff) && $buff['suspended'])
        {
            continue;
        }

        // Suspend non pvp allowed buffs when in pvp
        if ($susp && (! isset($buff[$susp]) || ! $buff[$susp]))
        {
            $session['bufflist'][$key]['suspended'] = 1;
            $suspendnotify = 1;
        }

        // reset the 'used this round state'
        $buff['used'] = 0;
    }

    if ($suspendnotify)
    {
        if (false === $msg)
        {
            $msg = '`&The gods have suspended some of your enhancements!`n';
        }

        $lotgdBattleContent['battlerounds'][$countround]['allied'][] = sanitize_mb($msg);
    }
}

function suspend_buff_by_name($name, $msg = false)
{
    global $session, $countround, $lotgdBattleContent;

    // If it's not already suspended.
    if ($session['bufflist'][$name] && ! $session['bufflist'][$name]['suspended'])
    {
        $session['bufflist'][$name]['suspended'] = 1;

        // And notify.
        if (false === $msg)
        {
            $msg = '`&The gods have suspended some of your enhancements!`n';
        }

        $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
    }
}

function unsuspend_buff_by_name($name, $msg = false)
{
    global $session, $countround, $lotgdBattleContent;

    // If it's not already suspended.
    if ($session['bufflist'][$name] && $session['bufflist'][$name]['suspended'])
    {
        $session['bufflist'][$name]['suspended'] = 0;

        // And notify.
        if (false === $msg)
        {
            $msg = '`&The gods have restored all suspended enhancements.`n`n';
        }

        $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
    }
}

function is_buff_active($name)
{
    global $session;
    // If it's not already suspended.
    return ($session['bufflist'][$name] && ! $session['bufflist'][$name]['suspended']) ? 1 : 0;
}

function unsuspend_buffs($susp = false, $msg = false)
{
    global $session, $badguy, $countround, $lotgdBattleContent;

    $unsuspendnotify = 0;
    reset($session['bufflist']);

    foreach ($session['bufflist'] as $key => $buff)
    {
        if (array_key_exists('expireafterfight', $buff) && $buff['expireafterfight'])
        {
            unset($session['bufflist'][$key]);
        }
        elseif (array_key_exists('suspended', $buff) && $buff['suspended'] && $susp && (! array_key_exists($susp, $buff) || ! $buff[$susp]))
        {
            $session['bufflist'][$key]['suspended'] = 0;
            $unsuspendnotify = 1;
        }
    }

    if ($unsuspendnotify)
    {
        if (false === $msg)
        {
            $msg = '`&The gods have restored all suspended enhancements.`n`n';
        }

        $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
    }
}

function apply_bodyguard($level)
{
    global $session, $badguy;

    if (! isset($session['bufflist']['bodyguard']))
    {
        switch ($level)
        {
            case 1:
                $badguyatkmod = 1.05;
                $defmod = 0.95;
                $rounds = -1;
            break;
            case 2:
                $badguyatkmod = 1.1;
                $defmod = 0.9;
                $rounds = -1;
            break;
            case 3:
                $badguyatkmod = 1.2;
                $defmod = 0.8;
                $rounds = -1;
            break;
            case 4:
                $badguyatkmod = 1.3;
                $defmod = 0.7;
                $rounds = -1;
            break;
            case 5:
                $badguyatkmod = 1.4;
                $defmod = 0.6;
                $rounds = -1;
            break;
        }

        apply_buff('bodyguard', [
            'startmsg' => "`\${badguy}'s bodyguard protects them!`0",
            'name' => '`&Bodyguard`0',
            'wearoff' => 'The bodyguard seems to have fallen asleep.',
            'badguyatkmod' => $badguyatkmod,
            'defmod' => $defmod,
            'rounds' => $rounds,
            'allowinpvp' => 1,
            'expireafterfight' => 1,
            'schema' => 'pvp'
        ]
        );
    }
}

function apply_skill($skill, $l)
{
    global $session;

    if ('godmode' == $skill)
    {
        apply_buff('godmode', [
            'name' => '`&GOD MODE`0',
            'rounds' => 1,
            'wearoff' => 'You feel mortal again.',
            'atkmod' => 25,
            'defmod' => 25,
            'invulnerable' => 1,
            'startmsg' => '`&`bYou feel godlike.´b',
            'schema' => 'skill'
        ]);
    }

    modulehook('apply-specialties');
}
