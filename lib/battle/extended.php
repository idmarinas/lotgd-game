<?php

// addnews ready
// mail ready
// translation ready
//

/**
 * Prepare all data for show battle bars.
 *
 * @param array $enemies
 *
 * @return array
 */
function prepare_data_battlebars(array $enemies)
{
    global $enemycounter, $companions, $session;

    $user = &$session['user']; //fast and better
    $data = [];

    $barDisplay = (int) ($user['prefs']['forestcreaturebar'] ?? getsetting('forestcreaturebar', 0));
    $user['prefs']['forestcreaturebar'] = $barDisplay;

    $hitpointstext = 'Soulpoints';
    $healthtext = '`)Soul`0';
    if ($user['alive'])
    {
        $hitpointstext = 'Hitpoints';
        $healthtext = '`^Health`0';
    }

    $data['enemies'] = [];
    //-- Prepare data for enemies
    foreach ($enemies as $index => $badguy)
    {
        $ccode = '`2';
        if ((isset($badguy['istarget']) && true == $badguy['istarget']) && $enemycounter > 1)
        {
            $ccode = '`#';
        }

        if (isset($badguy['hidehitpoints']) && true == $badguy['hidehitpoints'])
        {
            $maxhealth = $health = '???';
        }
        else
        {
            $health = $badguy['creaturehealth'];
            $maxhealth = $badguy['creaturemaxhealth'];
        }

        $data['enemies'][$index] = [
            'showbar' => false,
            'showhptext' => true,
            'who' => '`$Enemy`0',
            'isTarget' => (isset($badguy['istarget']) && $badguy['istarget'] && $enemycounter > 1),
            'name' => $ccode.$badguy['creaturename'].$ccode,
            'level' => $badguy['creaturelevel'],
            'hitpointstext' => $hitpointstext,
            'healthtext' => $healthtext,
            'hpvalue' => $badguy['creaturehealth'], //-- Real health of creature
            'hptotal' => $badguy['creaturemaxhealth'], //-- Real max health of creature
            'hpvaluetext' => $health,
            'hptotaltext' => $maxhealth
        ];

        if (1 == $barDisplay)
        {
            $data['enemies'][$index]['showhptext'] = false;
            $data['enemies'][$index]['showbar'] = true;
        }
        elseif (2 == $barDisplay)
        {
            $data['enemies'][$index]['showbar'] = true;
        }
    }

    //-- Prepare data for player
    if ($user['alive'])
    {
        $hitpointstext = $user['name'].'`0';
        $dead = false;
    }
    else
    {
        $hitpointstext = ['Soul of %s', $user['name']];
        $dead = true;
        $maxsoul = 50 + 10 * $user['level'] + $user['dragonkills'] * 2;
    }

    $data['user'] = [
        'showbar' => false,
        'showhptext' => true,
        'who' => translate_inline('`!You`0'),
        'isTarget' => false,
        'name' => $hitpointstext,
        'level' => $user['level'],
        'healthtext' => $healthtext,
        'hpvalue' => $user['hitpoints'],
        'hptotal' => (! $dead ? $user['maxhitpoints'] : $maxsoul),
        'hpvaluetext' => $user['hitpoints'],
        'hptotaltext' => (! $dead ? $user['maxhitpoints'] : $maxsoul)
    ];

    if (1 == $barDisplay)
    {
        $data['user']['showhptext'] = false;
        $data['user']['showbar'] = true;
    }
    elseif (2 == $barDisplay)
    {
        $data['user']['showbar'] = true;
    }

    //-- Prepare data for companions
    $data['companions'] = [];

    foreach ($companions as $index => $companion)
    {
        $ccode = '`2';

        if (isset($companion['hidehitpoints']) && true == $companion['hidehitpoints'])
        {
            $maxhealth = $health = '???';
        }
        else
        {
            $health = $companion['hitpoints'];
            $maxhealth = $companion['maxhitpoints'];
        }

        $data['companions'][$index] = [
            'showbar' => false,
            'showhptext' => true,
            'who' => translate_inline('`^Companion`0'),
            'isTarget' => (isset($companion['istarget']) && $companion['istarget'] && $enemycounter > 1),
            'name' => $ccode.$companion['name'].$ccode,
            'level' => $session['user']['level'],
            'hitpointstext' => $hitpointstext,
            'healthtext' => $healthtext,
            'hpvalue' => $companion['hitpoints'], //-- Real health of companion
            'hptotal' => $companion['maxhitpoints'], //-- Real max health of creature
            'hpvaluetext' => $health,
            'hptotaltext' => $maxhealth
        ];

        if (1 == $barDisplay)
        {
            $data['companions'][$index]['showhptext'] = false;
            $data['companions'][$index]['showbar'] = true;
        }
        elseif (2 == $barDisplay)
        {
            $data['companions'][$index]['showbar'] = true;
        }
    }

    return $data;
}

/**
 * This function prepares the fight, sets up options and gives hook a hook to change options on a per-player basis.
 *
 * @param array $options the options given by a module or basics
 *
 * @return array the complete options
 */
function prepare_fight($options = [])
{
    global $companions;

    $basicoptions = [
        'maxattacks' => getsetting('maxattacks', 4),
    ];

    if (! is_array($options))
    {
        $options = [];
    }

    $fightoptions = $options + $basicoptions;
    $fightoptions = modulehook('fightoptions', $fightoptions);

    // We'll also reset the companions here...
    prepare_companions();

    return $fightoptions;
}

/**
 * This functions prepares companions to be able to take part in a fight. Uses global copies.
 */
function prepare_companions()
{
    global $companions;

    $newcompanions = [];

    if (is_array($companions))
    {
        foreach ($companions as $name => $companion)
        {
            if (! isset($companion['suspended']) || false == $companion['suspended'])
            {
                $companion['used'] = false;
            }

            $newcompanions[$name] = $companion;
        }
    }

    $companions = $newcompanions;
}

/**
 * Suspends companions on a given parameter.
 *
 * @param string $susp  The type of suspension
 * @param string $nomsg The message to be displayed upon suspending. If false, no message will be displayed.
 */
function suspend_companions($susp, $nomsg = null)
{
    global $companions, $countround, $lotgdBattleContent;

    $newcompanions = [];
    $suspended = false;

    if (is_array($companions))
    {
        foreach ($companions as $name => $companion)
        {
            if ($susp)
            {
                if (! isset($companion[$susp]) || true != $companion[$susp])
                {
                    if (! isset($companion['suspended']) || true != $companion['suspended'])
                    {
                        $suspended = true;
                        $companion['suspended'] = true;
                    }
                }
            }

            $newcompanions[$name] = $companion;
        }
    }

    if ($suspended)
    {
        if (false === $nomsg || null === $nomsg)
        {
            $nomsg = '`&Your companions stand back during this fight!`n';
        }

        if ($nomsg)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $nomsg;
        }
    }

    $companions = $newcompanions;
}

/**
 * Enables suspended companions.
 *
 * @param string $susp  The type of suspension
 * @param string  $nomsg The message to be displayed upon unsuspending. If false, no message will be displayed.
 */
function unsuspend_companions($susp, $nomsg = null)
{
    global $companions, $countround, $lotgdBattleContent;

    $notify = false;
    $newcompanions = [];

    if (is_array($companions))
    {
        foreach ($companions as $name => $companion)
        {
            if (isset($companion['suspended']) && true == $companion['suspended'])
            {
                $notify = true;
                $companion['suspended'] = false;
            }

            $newcompanions[$name] = $companion;
        }
    }

    if ($notify && false !== $nomsg)
    {
        if (null === $nomsg || false === $nomsg)
        {
            $nomsg = '`&Your companions return to stand by your side!`n';
        }

        if ($nomsg)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $nomsg;
        }
    }

    $companions = $newcompanions;
}

/**
 * Automatically chooses the first still living enemy as target for attacks.
 *
 * @param array $localenemies the stack of enemies to find a valid one from
 *
 * @return array $localenemies the stack with changed targetting
 */
function autosettarget($localenemies)
{
    $targetted = 0;

    if (is_array($localenemies))
    {
        foreach ($localenemies as $index => $badguy)
        {
            $localenemies[$index] += ['dead' => false, 'istarget' => false]; // This line will add these two indices if they haven't been set.

            if (1 == count($localenemies))
            {
                $localenemies[$index]['istarget'] = true;
            }

            if (true == $localenemies[$index]['istarget'] && false == $localenemies[$index]['dead'])
            {
                $targetted++;
            }
        }
    }

    if (! $targetted && is_array($localenemies))
    {
        foreach ($localenemies as $index => $badguy)
        {
            if (false == $localenemies[$index]['dead'] && (! isset($badguy['cannotbetarget']) || false === $badguy['cannotbetarget']))
            {
                $localenemies[$index]['istarget'] = true;
                $targetted = true;

                break;
            }
            else
            {
                continue;
            }
        }
    }

    return $localenemies;
}

/**
 * Based upon the type of the companion different actions are performed and the companion is marked as "used" after that.
 *
 * @param array  $companion The companion itself
 * @param string $activate  The stage of activation. Can be one of these: "fight", "defend", "heal" or "magic".
 *
 * @return array The changed companion
 */
function report_companion_move($companion, $activate = 'fight')
{
    global $badguy, $session, $creatureattack, $creatureatkmod, $adjustment;
    global $creaturedefmod, $defmod, $atkmod, $atk, $def, $countround, $defended, $needtosstopfighting, $lotgdBattleContent;

    if (isset($companion['suspended']) && true == $companion['suspended'])
    {
        return $companion;
    }

    if ('fight' == $activate && (bool) ($companion['abilities']['fight'] ?? false) && false == $companion['used'])
    {
        $roll = rollcompaniondamage($companion);

        $damage_done = $roll['creaturedmg'];
        $damage_received = $roll['selfdmg'];

        if (0 == $damage_done)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`^%s`4 tries to hit %s but `$MISSES!`n', $companion['name'], $badguy['creaturename']];
        }
        elseif ($damage_done < 0)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`^%s`4 tries to hit %s but %s `$RIPOSTES`4 for `^%s`4 points of damage!`n', $companion['name'], $badguy['creaturename'], $badguy['creaturename'], abs($damage_done)];
            $companion['hitpoints'] += $damage_done;
        }
        else
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`^%s`4 hits %s for `^%s`4 points of damage!`n', $companion['name'], $badguy['creaturename'], $damage_done];
            $badguy['creaturehealth'] -= $damage_done;
        }

        if ($badguy['creaturehealth'] >= 0)
        {
            if (0 == $damage_received)
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 tries to hit `$%s`4 but `^MISSES!`n', $badguy['creaturename'], $companion['name']];
            }
            elseif ($damage_received < 0)
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 tries to hit `$%s`4 but %s `^RIPOSTES`4 for `^%s`4 points of damage!`n', $badguy['creaturename'], $companion['name'], $companion['name'], abs($damage_received)];
                $badguy['creaturehealth'] += $damage_received;
            }
            else
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 hits `$%s`4 for `$%s`4 points of damage!`n', $badguy['creaturename'], $companion['name'], $damage_received];
                $companion['hitpoints'] -= $damage_received;
            }
        }

        $companion['used'] = true;
    }
    elseif ('heal' == $activate && isset($companion['abilities']['heal']) && true == $companion['abilities']['heal'] && false == $companion['used'])
    {
        // This one will be tricky! We are looking for the first target which can be healed. This can be the player himself
        // or any other companion or our fellow companion himself.
        // But if our little friend is the second companion, all other companions will have been copied to the newenemies
        // array already  ...
        if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
        {
            $hptoheal = min($companion['abilities']['heal'], $session['user']['maxhitpoints'] - $session['user']['hitpoints']);
            $session['user']['hitpoints'] += $hptoheal;
            $companion['used'] = true;
            $msg = isset($companion['healmsg']) ? $companion['healmsg'] : '';

            if ('' == $msg)
            {
                $msg = '{companion} heals your wounds. You regenerate {damage} hitpoints.';
            }
            $msg = substitute_array('`)'.$msg.'`0`n', ['{companion}', '{damage}'], [$companion['name'], $hptoheal]);

            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
        }
        else
        {
            // Okay. We really have to do this :(
            global $newcompanions;

            $mynewcompanions = $newcompanions;

            if (! is_array($mynewcompanions))
            {
                $mynewcompanions = [];
            }
            $healed = false;

            foreach ($mynewcompanions as $myname => $mycompanion)
            {
                if ($mycompanion['hitpoints'] >= $mycompanion['maxhitpoints'] || $healed || (isset($companion['cannotbehealed']) && true == $companion['cannotbehealed']))
                {
                    continue;
                }
                else
                {
                    $hptoheal = min($companion['abilities']['heal'], $mycompanion['maxhitpoints'] - $mycompanion['hitpoints']);
                    $mycompanion['hitpoints'] += $hptoheal;
                    $companion['used'] = true;
                    $msg = $companion['healcompanionmsg'] ?? '';

                    if ('' == $msg)
                    {
                        $msg = "{companion} heals {target}'s wounds. {target} regenerates {damage} hitpoints.";
                    }
                    $msg = substitute_array('`)'.$msg.'`0`n', ['{companion}', '{damage}', '{target}'], [$companion['name'], $hptoheal, $mycompanion['name']]);

                    $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
                    $healed = true;
                    $newcompanions[$myname] = $mycompanion;
                }
            }

            if (! $healed)
            {
                global $companions, $name;

                $mycompanions = $companions;
                $foundmyself = false;

                foreach ($mycompanions as $myname => $mycompanion)
                {
                    if (! $foundmyself || (isset($companion['cannotbehealed']) && true == $companion['cannotbehealed']))
                    {
                        if ($myname == $name)
                        {
                            $foundmyself = true;
                        }

                        continue;
                    }
                    else
                    {
                        //There's someone hiding behind us...
                        foreach ($mycompanions as $myname => $mycompanion)
                        {
                            if ($mycompanion['hitpoints'] >= $mycompanion['maxhitpoints'] || $healed)
                            {
                                continue;
                            }
                            else
                            {
                                $hptoheal = min($companion['abilities']['heal'], $mycompanion['maxhitpoints'] - $mycompanion['hitpoints']);
                                $mycompanion['hitpoints'] += $hptoheal;
                                $companion['used'] = true;
                                $msg = $companion['healcompanionmsg'] ?? '';

                                if ('' == $msg)
                                {
                                    $msg = "{companion} heals {target}'s wounds. {target} regenerates {damage} hitpoints.";
                                }
                                $msg = substitute_array('`)'.$msg.'`0`n', ['{companion}', '{damage}', '{target}'], [$companion['name'], $hptoheal, $mycompanion['name']]);

                                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;

                                $healed = true;
                                $companions[$myname] = $mycompanion;
                            } // else	// These
                        } // foreach	// are
                    } // else			// some
                } // foreach			// totally
            } // if						// senseless
        } // else						// comments.
        unset($mynewcompanions, $mycompanions);

        $roll = rollcompaniondamage($companion);
        $damage_done = $roll['creaturedmg'];
        $damage_received = $roll['selfdmg'];

        if ($badguy['creaturehealth'] >= 0)
        {
            if (0 == $damage_received)
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 tries to hit `$%s`4 but `^MISSES!`n', $badguy['creaturename'], $companion['name']];
            }
            elseif ($damage_received < 0)
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 tries to hit `$%s`4 but %s `^RIPOSTES`4 for `^%s`4 points of damage!`n', $badguy['creaturename'], $companion['name'], $companion['name'], abs($damage_received)];

                $badguy['creaturehealth'] += $damage_received;
            }
            else
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 hits `$%s`4 for `$%s`4 points of damage!`n', $badguy['creaturename'], $companion['name'], $damage_received];

                $companion['hitpoints'] -= $damage_received;
            }
        }
        $companion['used'] = true;
    }
    elseif ('defend' == $activate && isset($companion['abilities']['defend']) && true == $companion['abilities']['defend'] && false == $defended && false == $companion['used'])
    {
        $defended = 1;
        $roll = rollcompaniondamage($companion);
        $damage_done = $roll['creaturedmg'];
        $damage_received = $roll['selfdmg'];

        if (0 == $damage_done)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`^%s`4 tries to hit %s but `^MISSES!`n', $companion['name'], $badguy['creaturename']];
        }
        elseif ($damage_done < 0)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`^%s`4 tries to hit %s but %s `^RIPOSTES`4 for `^%s`4 points of damage!`n', $companion['name'], $badguy['creaturename'], $badguy['creaturename'], abs($damage_done)];

            $companion['hitpoints'] += $damage_done;
        }
        else
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = ['`^%s`4 hits %s for `$%s`4 points of damage!`n', $companion['name'], $badguy['creaturename'], $damage_done];

            $badguy['creaturehealth'] -= $damage_done;
        }

        if ($badguy['creaturehealth'] >= 0)
        {
            if (0 == $damage_received)
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 tries to hit `$%s`4 but `^MISSES!`n', $badguy['creaturename'], $companion['name']];
            }
            elseif ($damage_received < 0)
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 tries to hit `$%s`4 but %s `^RIPOSTES`4 for `^%s`4 points of damage!`n', $badguy['creaturename'], $companion['name'], $companion['name'], abs($damage_received)];

                $badguy['creaturehealth'] += $damage_received;
            }
            else
            {
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`4 hits `$%s`4 for `$%s`4 points of damage!`n', $badguy['creaturename'], $companion['name'], $damage_received];
                $companion['hitpoints'] -= $damage_received;
            }
        }

        $companion['used'] = true;
    }
    elseif ('magic' == $activate && isset($companion['abilities']['magic']) && true == $companion['abilities']['magic'] && false == $companion['used'])
    {
        $roll = rollcompaniondamage($companion);
        $damage_done = abs($roll['creaturedmg']);

        if (0 == $damage_done)
        {
            $msg = $companion['magicfailmsg'];

            if ('' == $msg)
            {
                $msg = '{companion} shoots a magical arrow at {badguy} but misses.';
            }
            $msg = substitute_array('`)'.$msg.'`0`n', ['{companion}'], [$companion['name']]);

            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
        }
        else
        {
            if (isset($companion['magicmsg']))
            {
                $msg = $companion['magicmsg'];
            }
            else
            {
                $msg = '{companion} shoots a magical arrow at {badguy} and deals {damage} damage.';
            }
            $msg = substitute_array('`)'.$msg.'`0`n', ['{companion}', '{damage}'], [$companion['name'], $damage_done]);

            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;

            $badguy['creaturehealth'] -= $damage_done;
        }

        $companion['hitpoints'] -= $companion['abilities']['magic'];
        $companion['used'] = true;
    }

    if ($badguy['creaturehealth'] <= 0)
    {
        $badguy['dead'] = true;
        $badguy['istarget'] = false;
        $count = 1;
        $needtosstopfighting = true;
    }

    if ($companion['hitpoints'] <= 0)
    {
        if (isset($companion['dyingtext']) && $companion['dyingtext'] > '')
        {
            $msg = $companion['dyingtext'];
        }
        else
        {
            $msg = '`5Your companion catches his last breath before it dies.';
        }

        $lotgdBattleContent['battlerounds'][$countround]['allied'][] = substitute_array("`){$msg}`0`n", ['{companion}'], [$companion['name']]);

        if (isset($companion['cannotdie']) && true == $companion['cannotdie'])
        {
            $companion['hitpoints'] = 0;
        }
        else
        {
            return false;
        }
    }

    return $companion;
}

/**
 * Based upon the companion's stats damage values are calculated.
 *
 * @param array $companion
 *
 * @return array
 */
function rollcompaniondamage($companion)
{
    global $badguy,$creatureattack, $creatureatkmod,$adjustment,$options;
    global $creaturedefmod,$compdefmod,$compatkmod,$buffset,$atk,$def;

    $creaturedmg = 0;
    $selfdmg = 0;
    if ($badguy['creaturehealth'] > 0 && $companion['hitpoints'] > 0)
    {
        $adjustedcreaturedefense = ($creaturedefmod * $badguy['creaturedefense'] / ($adjustment * $adjustment));
        if ('pvp' == $options['type'])
        {
            $adjustedcreaturedefense = $badguy['creaturedefense'];
        }

        $creatureattack = $badguy['creatureattack'] * $creatureatkmod;
        $adjustedselfdefense = ($companion['defense'] * $adjustment * $compdefmod);

        /*
        debug("Base creature defense: " . $badguy['creaturedefense']);
        debug("Creature defense mod: $creaturedefmod");
        debug("Adjustment: $adjustment");
        debug("Adjusted creature defense: $adjustedcreaturedefense");
        debug("Adjusted creature attack: $creatureattack");
        debug("Adjusted self defense: $adjustedselfdefense");
        */

        while (! isset($creaturedmg) || ! isset($selfdmg) || 0 == $creaturedmg && 0 == $selfdmg)
        {
            $atk = $companion['attack'] * $compatkmod;

            if (1 == e_rand(1, 20) && 'pvp' != $options['type'])
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
            }

            if ($creaturedmg > 0)
            {
                $creaturedmg = round($buffset['compdmgmod'] * $creaturedmg, 0);
            }

            $pdefroll = bell_rand(0, $adjustedselfdefense);
            $catkroll = bell_rand(0, $creatureattack);
            /*
               debug("Creature attack roll: $catkroll");
               debug("Player defense roll: $pdefroll");
             */
            $selfdmg = 0 - (int) ($pdefroll - $catkroll);

            if ($selfdmg < 0)
            {
                $selfdmg = (int) ($selfdmg / 2);
                $selfdmg = round($selfdmg * $buffset['compdmgmod'], 0);
            }

            if ($selfdmg > 0)
            {
                $selfdmg = round($selfdmg * $buffset['badguydmgmod'], 0);
            }
        }
    }

    // Handle god mode's invulnerability
    if ($buffset['invulnerable'])
    {
        $creaturedmg = abs($creaturedmg);
        $selfdmg = -abs($selfdmg);
    }

    return ['creaturedmg' => (isset($creaturedmg) ? $creaturedmg : 0), 'selfdmg' => (isset($selfdmg) ? $selfdmg : 0)];
}

/**
 * Adds a new creature to the badguy array.
 *
 * @param mixed $creature A standard badguy array. If numeric, the corresponding badguy will be loaded from the database.
 */
function battle_spawn($creature)
{
    global $enemies, $newenemies, $badguy, $nextindex, $countround, $lotgdBattleContent;

    if (! is_array($newenemies))
    {
        $newenemies = [];
    }

    if (! isset($nextindex))
    {
        $nextindex = count($enemies);
    }
    else
    {
        $nextindex++;
    }

    if (is_numeric($creature))
    {
        $sql = 'SELECT * FROM '.DB::prefix('creatures')." WHERE creatureid = $creature LIMIT 1";
        $result = DB::query($sql);

        if ($row = DB::fetch_assoc($result))
        {
            $newenemies[$nextindex] = $row;
            $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`2 summons `^%s`2 for help!`n', $badguy['creaturename'], $row['creaturename']];
        }
    }
    elseif (is_array($creature))
    {
        $newenemies[$nextindex] = $creature;
    }

    ksort($newenemies);
}

/**
 * Allows creatures to heal themselves or another badguy.
 *
 * @param int   $amount Amount of helath to be restored
 * @param mixed $target if false badguy will heal itself otherwise the enemy with this index
 */
function battle_heal($amount, $target = false)
{
    global $newenemies, $enemies, $badguy, $countround, $lotgdBattleContent;

    if ($amount > 0)
    {
        if (false === $target)
        {
            $badguy['creaturehealth'] += $amount;
            $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`2 heals itself for `^%s`2 hitpoints.', $badguy['creaturename'], $amount];
        }
        else
        {
            if (isset($newenemies[$target]))
            {
                // Target had its turn already...
                if (false == $newenemies[$target]['dead'])
                {
                    $newenemies[$target]['creaturehealth'] += $amount;
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`2 heal `^%s`2 for `^%s`2 hitpoints.', $badguy['creaturename'], $newenemies[$target]['creaturename'], $amount];
                }
            }
            else
            {
                if (false == $enemies[$target]['dead'])
                {
                    $enemies[$target]['creaturehealth'] += $amount;
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = ['`^%s`2 heal `^%s`2 for `^%s`2 hitpoints.', $badguy['creaturename'], $enemies[$target]['creaturename'], $amount];
                }
            }
        }
    }
}

/**
 * Executes the given script or loads the script and then executes it.
 *
 * @param string $script the script to be executed
 */
function execute_ai_script($script)
{
    global $unsetme;

    if ($script > '')
    {
        eval($script);
    }
}
