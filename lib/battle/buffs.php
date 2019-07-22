<?php

// translator ready
// addnews ready
// mail ready
/**
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2009, Dragonprime Development Team
 *
 * @version Lotgd 1.1.2 DragonPrime Edition
 *
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
require_once 'lib/substitute.php';

function activate_buffs($tag)
{
    global $session, $badguy, $countround, $lotgdBattleContent;

    reset($session['bufflist']);

    $result = [];
    $result['invulnerable'] = 0;
    $result['dmgmod'] = 1;
    $result['compdmgmod'] = 1;
    $result['badguydmgmod'] = 1;
    $result['atkmod'] = 1;
    $result['compatkmod'] = 1;
    $result['badguyatkmod'] = 1;
    $result['defmod'] = 1;
    $result['compdefmod'] = 1;
    $result['badguydefmod'] = 1;
    $result['lifetap'] = [];
    $result['dmgshield'] = [];

    foreach ($session['bufflist'] as $key => $buff)
    {
        if (array_key_exists('suspended', $buff) && $buff['suspended'])
        {
            continue;
        }

        if (isset($buff['startmsg']))
        {
            if (is_array($buff['startmsg']))
            {
                $buff['startmsg'] = str_replace('`%', '`%%', $buff['startmsg']);
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = substitute("`5{$buff['startmsg']}`0`n");
            }
            else
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = substitute("`5{$buff['startmsg']}`0`n");
            }

            unset($session['bufflist'][$key]['startmsg']);
        }

        // Figure out activate based on buff features
        $activate = false;

        if ('roundstart' == $tag)
        {
            if (isset($buff['regen']))
            {
                $activate = true;
            }

            if (isset($buff['minioncount']))
            {
                $activate = true;
            }
        }
        elseif ('offense' == $tag)
        {
            if (isset($buff['invulnerable']) && $buff['invulnerable'])
            {
                $activate = true;
            }

            if (isset($buff['atkmod']))
            {
                $activate = true;
            }

            if (isset($buff['dmgmod']))
            {
                $activate = true;
            }

            if (isset($buff['badguydefmod']))
            {
                $activate = true;
            }

            if (isset($buff['lifetap']))
            {
                $activate = true;
            }

            if (isset($buff['damageshield']))
            {
                $activate = true;
            }
        }
        elseif ('defense' == $tag)
        {
            if (isset($buff['invulnerable']) && $buff['invulnerable'])
            {
                $activate = true;
            }

            if (isset($buff['defmod']))
            {
                $activate = true;
            }

            if (isset($buff['badguyatkmod']))
            {
                $activate = true;
            }

            if (isset($buff['badguydmgmod']))
            {
                $activate = true;
            }

            if (isset($buff['lifetap']))
            {
                $activate = true;
            }

            if (isset($buff['damageshield']))
            {
                $activate = true;
            }
        }

        // If this should activate now and it hasn't already activated,
        // do the round message and mark it.
        if ($activate && (! array_key_exists('used', $buff) || ! $buff['used']))
        {
            // mark it used.
            $session['bufflist'][$key]['used'] = 1;
            // if it has a 'round message', run it.
            if (isset($buff['roundmsg']))
            {
                if (is_array($buff['roundmsg']))
                {
                    $buff['roundmsg'] = str_replace('`%', '`%%', $buff['roundmsg']);
                    $lotgdBattleContent['battlerounds'][$countround]['allied'][] = substitute("`5{$buff['roundmsg']}`0`n");
                }
                else
                {
                    $lotgdBattleContent['battlerounds'][$countround]['allied'][] = substitute("`5{$buff['roundmsg']}`0`n");
                }
            }
        }

        // Now, calculate any effects and run them if needed.
        if (isset($buff['invulnerable']) && $buff['invulnerable'])
        {
            $result['invulnerable'] = 1;
        }

        if (isset($buff['atkmod']))
        {
            $result['atkmod'] *= $buff['atkmod'];

            if (isset($buff['aura']) && $buff['aura'])
            {
                $result['compatkmod'] *= $buff['atkmod'];
            }
        }

        if (isset($buff['badguyatkmod']))
        {
            $result['badguyatkmod'] *= $buff['badguyatkmod'];
        }

        if (isset($buff['defmod']))
        {
            $result['defmod'] *= $buff['defmod'];

            if (isset($buff['aura']) && $buff['aura'])
            {
                $result['compdefmod'] *= $buff['defmod'];
            }
        }

        if (isset($buff['badguydefmod']))
        {
            $result['badguydefmod'] *= $buff['badguydefmod'];
        }

        if (isset($buff['dmgmod']))
        {
            $result['dmgmod'] *= $buff['dmgmod'];

            if (isset($buff['aura']) && $buff['aura'])
            {
                $result['compdmgmod'] *= $buff['dmgmod'];
            }
        }

        if (isset($buff['badguydmgmod']))
        {
            $result['badguydmgmod'] *= $buff['badguydmgmod'];
        }

        if (isset($buff['lifetap']))
        {
            array_push($result['lifetap'], $buff);
        }

        if (isset($buff['damageshield']))
        {
            array_push($result['dmgshield'], $buff);
        }

        if (isset($buff['regen']) && 'roundstart' == $tag && true == $badguy['istarget'])
        {
            $hptoregen = (int) $buff['regen'];
            $hpdiff = $session['user']['maxhitpoints'] - $session['user']['hitpoints'];
            // Don't regen if we are above max hp
            if ($hpdiff < 0)
            {
                $hpdiff = 0;
            }

            if ($hpdiff < $hptoregen)
            {
                $hptoregen = $hpdiff;
            }
            $session['user']['hitpoints'] += $hptoregen;
            // Now, take abs value just incase this was a damaging buff
            $hptoregen = abs($hptoregen);

            $msg = $buff['effectmsg'];
            if (0 == $hptoregen)
            {
                $msg = $buff['effectnodmgmsg'] ?? '';
            }

            if (is_array($msg))
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = substitute("`)$msg`0`n", ['{damage}'], [$hptoregen]);
            }
            elseif ('' != $msg)
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = substitute("`)$msg`0`n", ['{damage}'], [$hptoregen]);
            }

            if (isset($buff['aura']) && true == $buff['aura'])
            {
                global $companions;

                $auraeffect = (int) round($buff['regen'] / 3);

                if (is_array($companions) && count($companions) > 0 && 0 != $auraeffect)
                {
                    foreach ($companions as $name => $companion)
                    {
                        $unset = false;
                        // if a companion is damaged AND ( a companion ist still alive OR ( a companion is unconscious AND it's a healing effect))
                        if ($companion['hitpoints'] < $companion['maxhitpoints'] && ($companion['hitpoints'] > 0 || (true == $companion['cannotdie'] && $auraeffect > 0)))
                        {
                            $hptoregen = min($auraeffect, $companion['maxhitpoints'] - $companion['hitpoints']);
                            $companions[$name]['hitpoints'] += $hptoregen;
                            $msg = substitute("`){$buff['auramsg']}`0`n", ['{damage}', '{companion}'], [$hptoregen, $companion['name']]);
                            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;

                            if ($hptoregen < 0 && $companion['hitpoints'] <= 0)
                            {
                                if (isset($companion['dyingtext']))
                                {
                                    $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $companion['dyingtext'];
                                }

                                if (isset($companion['cannotdie']) && true == $companion['cannotdie'])
                                {
                                    $companion['hitpoints'] = 0;
                                }
                                else
                                {
                                    $unset = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (isset($buff['minioncount']) && 'roundstart' == $tag && ((isset($buff['areadamage']) && true == $buff['areadamage']) || true == $badguy['istarget']) && false == $badguy['dead'])
        {
            $max = $buff['maxgoodguydamage'] ?? 0;
            $min = $buff['mingoodguydamage'] ?? 0;
            $who = 1;
            if (isset($buff['maxbadguydamage']) && 0 != $buff['maxbadguydamage'])
            {
                $max = $buff['maxbadguydamage'];
                $min = isset($buff['minbadguydamage']) ? $buff['minbadguydamage'] : 0;
                $who = 0;
            }
            $minioncounter = 1;

            while ($minioncounter <= $buff['minioncount'] && $who >= 0)
            {
                $damage = e_rand($min, $max);

                if (0 == $who)
                {
                    $badguy['creaturehealth'] -= $damage;

                    if ($badguy['creaturehealth'] <= 0)
                    {
                        $badguy['istarget'] = false;
                        $badguy['dead'] = true;
                    }
                }
                elseif (1 == $who)
                {
                    $session['user']['hitpoints'] -= $damage;
                }

                if ($damage < 0)
                {
                    $msg = $buff['effectfailmsg'] ?? '';
                }
                elseif (0 == $damage)
                {
                    $msg = $buff['effectnodmgmsg'] ?? '';
                }
                elseif ($damage > 0)
                {
                    $msg = $buff['effectmsg'] ?? '';
                }

                if (is_array($msg))
                {
                    $msg = substitute("`)$msg`0`n", ['{damage}'], [abs($damage)]);
                    $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg; //Here it's already translated
                }
                elseif ($msg > '')
                {
                    $msg = substitute("`)$msg`0`n", ['{damage}'], [abs($damage)]);
                    $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
                }

                if (true == $badguy['dead'])
                {
                    break;
                }

                $minioncounter++;
            }
        }
    }

    return $result;
}

function process_lifetaps($ltaps, $damage)
{
    global $session, $badguy, $countround, $lotgdBattleContent;

    foreach ($ltaps as $buff)
    {
        if (isset($buff['suspended']) && $buff['suspended'])
        {
            continue;
        }

        if ($buff['schema'])
        {
            tlschema($buff['schema']);
        }
        $healhp = $session['user']['maxhitpoints'] - $session['user']['hitpoints'];

        if ($healhp < 0)
        {
            $healhp = 0;
        }

        if (0 == $healhp)
        {
            $msg = $buff['effectnodmgmsg'] ?? '';
        }
        else
        {
            if ($healhp > $damage * $buff['lifetap'])
            {
                $healhp = round($damage * $buff['lifetap'], 0);
            }

            if ($healhp < 0)
            {
                $healhp = 0;
            }

            if ($healhp > 0)
            {
                $msg = $buff['effectmsg'] ?? '';
            }
            elseif (0 == $healhp)
            {
                $msg = $buff['effectfailmsg'] ?? '';
            }
        }
        $session['user']['hitpoints'] += $healhp;

        if (is_array($msg))
        {
            $msg = substitute("`){$msg}`0`n", ['{damage}'], [$healhp]);
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
        }
        elseif ($msg > '')
        {
            $msg = substitute("`){$msg}`0`n", ['{damage}'], [$healhp]);
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
        }

        if ($buff['schema'])
        {
            tlschema();
        }
    }
}

function process_dmgshield($dshield, $damage)
{
    global $badguy, $countround, $lotgdBattleContent;

    foreach ($dshield as $buff)
    {
        if (isset($buff['suspended']) && $buff['suspended'])
        {
            continue;
        }

        $realdamage = round($damage * $buff['damageshield'], 0);

        if ($realdamage < 0)
        {
            $realdamage = 0;
        }

        $msg = '';

        if ($realdamage > 0 && isset($buff['effectmsg']))
        {
            $msg = $buff['effectmsg'];
        }
        elseif (0 == $realdamage && isset($buff['effectfailmsg']))
        {
            $msg = $buff['effectfailmsg'];
        }
        elseif ($realdamage < 0 && isset($buff['effectfailmsg']))
        {
            $msg = $buff['effectfailmsg'];
        }

        $badguy['creaturehealth'] -= $realdamage;

        if ($badguy['creaturehealth'] <= 0)
        {
            $badguy['istarget'] = false;
            $badguy['dead'] = true;
        }

        if (is_array($msg))
        {
            $msg = substitute("`){$msg}`0`n", ['{damage}'], [$realdamage]);
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
        }
        elseif ($msg > '')
        {
            $msg = substitute("`){$msg}`0`n", ['{damage}'], [$realdamage]);
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
        }
    }
}

function expire_buffs()
{
    global $session, $countround, $lotgdBattleContent;

    foreach ($session['bufflist'] as $key => $buff)
    {
        if (array_key_exists('suspended', $buff) && $buff['suspended'])
        {
            continue;
        }

        if (array_key_exists('used', $buff) && $buff['used'])
        {
            $session['bufflist'][$key]['used'] = 0;

            if ($session['bufflist'][$key]['rounds'] > 0)
            {
                $session['bufflist'][$key]['rounds']--;
            }

            if (0 == (int) $session['bufflist'][$key]['rounds'])
            {
                if (isset($buff['wearoff']) && $buff['wearoff'])
                {
                    if (is_array($buff['wearoff']))
                    {
                        $buff['wearoff'] = str_replace('`%', '`%%', $buff['wearoff']);
                        $msg = substitute('`5'.$msg.'`0`n');
                    }
                    else
                    {
                        $msg = substitute('`5'.$buff['wearoff'].'`0`n');
                    }

                    $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
                }
                strip_buff($key);
            }
        }
    }
}

function expire_buffs_afterbattle()
{
    global $session, $countround, $lotgdBattleContent;

    //this is a copy of the expire_buffs, but only to be called at the very end of a battle to strip buffs that are only meant to last until a victory/defeat
    //redundant due to the nature of having a check at the beginning of a battle, and now one at the end.
    //use a buff flag 'expireafterfight' which you set to 1 or TRUE
    reset($session['bufflist']);

    foreach ($session['bufflist'] as $key => $buff)
    {
        if (array_key_exists('suspended', $buff) && $buff['suspended'])
        {
            continue;
        }

        if (array_key_exists('used', $buff) && $buff['used'])
        {
            if (array_key_exists('expireafterfight', $buff) && 1 == (int) $buff['expireafterfight'])
            {
                if (isset($buff['wearoff']) && $buff['wearoff'])
                {
                    if (is_array($buff['wearoff']))
                    {
                        $buff['wearoff'] = str_replace('`%', '`%%', $buff['wearoff']);
                        $msg = substitute("`5{$buff['wearoff']}`0`n");

                        $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
                    }
                    else
                    {
                        $msg = substitute("`5{$buff['wearoff']}`0`n");
                        $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
                    }
                }
                strip_buff($key);
            }
        }
    }
}
