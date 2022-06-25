<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Combat\Battle;

trait Buff
{
    private $buffStarted = [];

    public function activateBuffs($tag, &$badguy)
    {
        $result                 = [];
        $result['invulnerable'] = 0;
        $result['dmgmod']       = 1;
        $result['compdmgmod']   = 1;
        $result['badguydmgmod'] = 1;
        $result['atkmod']       = 1;
        $result['compatkmod']   = 1;
        $result['badguyatkmod'] = 1;
        $result['defmod']       = 1;
        $result['compdefmod']   = 1;
        $result['badguydefmod'] = 1;
        $result['lifetap']      = [];
        $result['dmgshield']    = [];

        foreach ($this->userBuffs as $key => &$buff)
        {
            if (\array_key_exists('suspended', $buff) && $buff['suspended'])
            {
                continue;
            }

            if (isset($buff['startmsg']) && ! ($this->buffStarted[$key] ?? false))
            {
                if (\is_array($buff['startmsg']))
                {
                    $buff['startmsg'] = \str_replace('`%', '`%%', $buff['startmsg']);
                }

                $this->addContextToRoundAlly([
                    $this->tools->substitute("`5{$buff['startmsg']}`0`n", ...$this->getSubstituteParams($badguy)),
                    [] ,
                    $this->getTranslationDomain()
                ]);

                $this->buffStarted[$key] = true;
            }

            // Figure out activate based on buff features
            $activate = false;

            if ('roundstart' == $tag)
            {
                if (isset($buff['regen']) || isset($buff['minioncount']))
                {
                    $activate = true;
                }
            }
            elseif ('offense' == $tag)
            {
                if (
                    (isset($buff['invulnerable']) && $buff['invulnerable'])
                    || isset($buff['atkmod'])
                    || isset($buff['dmgmod'])
                    || isset($buff['badguydefmod'])
                    || isset($buff['lifetap'])
                    || isset($buff['damageshield'])
                ) {
                    $activate = true;
                }
            }
            elseif ('defense' == $tag)
            {
                if (
                    (isset($buff['invulnerable']) && $buff['invulnerable'])
                    || isset($buff['defmod'])
                    || isset($buff['badguyatkmod'])
                    || isset($buff['badguydmgmod'])
                    || isset($buff['lifetap'])
                    || isset($buff['damageshield'])
                ) {
                    $activate = true;
                }
            }

            // If this should activate now and it hasn't already activated,
            // do the round message and mark it.
            if ($activate && ( ! \array_key_exists('used', $buff) || ! $buff['used']))
            {
                // mark it used.
                $buff['used'] = 1;
                // if it has a 'round message', run it.
                if (isset($buff['roundmsg']))
                {
                    if (\is_array($buff['roundmsg']))
                    {
                        $buff['roundmsg'] = \str_replace('`%', '`%%', $buff['roundmsg']);
                    }

                    $this->addContextToRoundAlly([
                        $this->tools->substitute("`5{$buff['roundmsg']}`0`n", ...$this->getSubstituteParams($badguy)),
                        [],
                        $this->getTranslationDomain()
                    ]);
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
                $result['lifetap'][] = $buff;
            }

            if (isset($buff['damageshield']))
            {
                $result['dmgshield'][] = $buff;
            }

            if (isset($buff['regen']) && 'roundstart' == $tag && $badguy['istarget'])
            {
                $hptoregen = (int) $buff['regen'];
                $hpdiff    = $this->user['maxhitpoints'] - $this->user['hitpoints'];
                // Don't regen if we are above max hp
                if ($hpdiff < 0)
                {
                    $hpdiff = 0;
                }

                if ($hpdiff < $hptoregen)
                {
                    $hptoregen = $hpdiff;
                }
                $this->user['hitpoints'] += $hptoregen;
                // Now, take abs value just incase this was a damaging buff
                $hptoregen = \abs($hptoregen);

                $msg = $buff['effectmsg'];

                if (0 == $hptoregen)
                {
                    $msg = $buff['effectnodmgmsg'] ?? '';
                }

                if ($msg)
                {
                    $this->addContextToRoundAlly([
                        $this->tools->substitute("`){$msg}`0`n", ...$this->getSubstituteParams($badguy, ['{damage}'], [$hptoregen])),
                        [],
                        $this->getTranslationDomain()
                    ]);
                }

                if (isset($buff['aura']) && $buff['aura'])
                {
                    global $companions;

                    $auraeffect = (int) \round($buff['regen'] / 3);

                    if (\is_array($companions) && ! empty($companions) && 0 != $auraeffect)
                    {
                        foreach ($companions as $name => $companion)
                        {
                            // if a companion is damaged AND ( a companion ist still alive OR ( a companion is unconscious AND it's a healing effect))
                            if ($companion['hitpoints'] < $companion['maxhitpoints'] && ($companion['hitpoints'] > 0 || ($companion['cannotdie'] && $auraeffect > 0)))
                            {
                                $hptoregen = \min($auraeffect, $companion['maxhitpoints'] - $companion['hitpoints']);
                                $companions[$name]['hitpoints'] += $hptoregen;
                                $msg = $this->tools->substitute(
                                    "`){$buff['auramsg']}`0`n",
                                    ...$this->getSubstituteParams($badguy, ['{damage}', '{companion}'], [$hptoregen, $companion['name']])
                                );

                                $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);

                                if ($hptoregen < 0 && $companion['hitpoints'] <= 0)
                                {
                                    if (isset($companion['dyingtext']))
                                    {
                                        $this->addContextToRoundAlly([$companion['dyingtext'], [], $this->getTranslationDomain()]);
                                    }

                                    if (isset($companion['cannotdie']) && $companion['cannotdie'])
                                    {
                                        $companion['hitpoints'] = 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (isset($buff['minioncount']) && 'roundstart' == $tag && ((isset($buff['areadamage']) && $buff['areadamage']) || $badguy['istarget']) && $badguy['dead'])
            {
                $max = $buff['maxgoodguydamage'] ?? 0;
                $min = $buff['mingoodguydamage'] ?? 0;
                $who = 1;

                if (isset($buff['maxbadguydamage']) && 0 != $buff['maxbadguydamage'])
                {
                    $max = $buff['maxbadguydamage'];
                    $min = $buff['minbadguydamage'] ?? 0;
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
                            $badguy['dead']     = true;
                        }
                    }
                    elseif (1 === $who)
                    {
                        $this->user['hitpoints'] -= $damage;
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

                    if ($msg > '')
                    {
                        $msg = $this->tools->substitute(
                            "`){$msg}`0`n",
                            ...$this->getSubstituteParams($badguy, ['{damage}'], [\abs($damage)])
                        );

                        $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
                    }

                    if ($badguy['dead'])
                    {
                        break;
                    }

                    ++$minioncounter;
                }
            }
        }

        return $result;
    }

    public function processLifeTaps($ltaps, $damage, $badguy)
    {
        foreach ($ltaps as $buff)
        {
            if (isset($buff['suspended']) && $buff['suspended'])
            {
                continue;
            }

            $healhp = \max(0, $this->user['maxhitpoints'] - $this->user['hitpoints']);

            if (0 == $healhp)
            {
                $msg = $buff['effectnodmgmsg'] ?? '';
            }
            else
            {
                if ($healhp > $damage * $buff['lifetap'])
                {
                    $healhp = \round($damage * $buff['lifetap'], 0);
                }

                $healhp = \max(0, $healhp);

                if ($healhp > 0)
                {
                    $msg = $buff['effectmsg'] ?? '';
                }
                elseif (0 == $healhp)
                {
                    $msg = $buff['effectfailmsg'] ?? '';
                }
            }
            $this->user['hitpoints'] += $healhp;

            if ($msg)
            {
                $msg = $this->tools->substitute(
                    "`){$msg}`0`n",
                    ...$this->getSubstituteParams($badguy, ['{damage}'], [$healhp])
                );
                $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
            }
        }
    }

    public function processDmgShield($dshield, $damage, &$badguy)
    {
        foreach ($dshield as $buff)
        {
            if (isset($buff['suspended']) && $buff['suspended'])
            {
                continue;
            }

            $realdamage = \max(0, \round($damage * $buff['damageshield'], 0));

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
                $badguy['dead']     = true;
            }

            if ($msg)
            {
                $msg = $this->tools->substitute(
                    "`){$msg}`0`n",
                    ...$this->getSubstituteParams($badguy, ['{damage}'], [$realdamage])
                );

                $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
            }
        }
    }

    public function expireBuffs()
    {
        foreach ($this->userBuffs as $key => &$buff)
        {
            if (\array_key_exists('suspended', $buff) && $buff['suspended'])
            {
                continue;
            }

            if (\array_key_exists('used', $buff) && $buff['used'])
            {
                $buff['used'] = 0;

                if ($buff['rounds'] > 0)
                {
                    --$buff['rounds'];
                }

                if (0 == (int) $buff['rounds'])
                {
                    if (isset($buff['wearoff']) && $buff['wearoff'])
                    {
                        $msg = $this->tools->substitute(
                            '`5'.$buff['wearoff'].'`0`n',
                            ...$this->getSubstituteParams($this->enemyTargeted)
                        );

                        $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
                    }
                    $this->stripBuff($key);
                }
            }
        }
    }

    public function expireBuffsAfterBattle()
    {
        //this is a copy of the expire_buffs, but only to be called at the very end of a battle to strip buffs that are only meant to last until a victory/defeat
        //redundant due to the nature of having a check at the beginning of a battle, and now one at the end.
        //use a buff flag 'expireafterfight' which you set to 1 or TRUE

        foreach ($this->userBuffs as $key => $buff)
        {
            if (\array_key_exists('suspended', $buff) && $buff['suspended'])
            {
                continue;
            }

            if (
                (\array_key_exists('used', $buff) && $buff['used'])
                && (\array_key_exists('expireafterfight', $buff) && 1 == (int) $buff['expireafterfight'])
            ) {
                if (isset($buff['wearoff']) && $buff['wearoff'])
                {
                    $msg = $this->tools->substitute(
                        "`5{$buff['wearoff']}`0`n",
                        ...$this->getSubstituteParams($this->enemyTargeted)
                    );

                    $this->addContextToRoundAlly([$msg, [], $this->getTranslationDomain()]);
                }

                $this->stripBuff($key);
            }
        }
    }
}
