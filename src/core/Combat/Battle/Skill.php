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

use Lotgd\Core\Event\Fight;

trait Skill
{
    public function rollDamage()
    {
        global $badguy, $session, $creatureattack, $creatureatkmod, $adjustment;
        global $creaturedefmod, $defmod, $atkmod, $buffset, $atk, $def, $options;

        $creaturedmg = 0;
        $selfdmg     = 0;

        if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0)
        {
            $adjustedcreaturedefense = $badguy['creaturedefense'];

            if ('pvp' != $options['type'])
            {
                $adjustedcreaturedefense = ($creaturedefmod * $badguy['creaturedefense'] / ($adjustment * $adjustment));
            }

            $creatureattack      = $badguy['creatureattack'] * $creatureatkmod;
            $adjustedselfdefense = ($this->playerFunction->getPlayerDefense() * $adjustment * $defmod);

            if ( ! isset($badguy['physicalresistance']))
            {
                $badguy['physicalresistance'] = 0;
            }
            $powerattack      = (int) $this->settings->getSetting('forestpowerattackchance', 10);
            $powerattackmulti = (float) $this->settings->getSetting('forestpowerattackmulti', 3);

            while ( ! isset($creaturedmg) || ! isset($selfdmg) || 0 == $creaturedmg && 0 == $selfdmg)
            {
                $atk = $this->playerFunction->getPlayerAttack() * $atkmod;

                if (1 == mt_rand(1, 20) && 'pvp' != $options['type'])
                {
                    $atk *= 3;
                }

                $patkroll = bell_rand(0, $atk);
                // Set up for crit detection
                $atk      = $patkroll;
                $catkroll = bell_rand(0, $adjustedcreaturedefense);

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

                $selfdmg = 0 - (int) ($pdefroll - $catkroll);

                if ($selfdmg < 0)
                {
                    $selfdmg = (int) ($selfdmg / 2);
                    $selfdmg = round($selfdmg * $buffset['dmgmod'], 0);
                    $selfdmg = min(0, round($selfdmg - ((int) $this->playerFunction->getPlayerPhysicalResistance()), 0));
                }

                if ($selfdmg > 0)
                {
                    $selfdmg = round($selfdmg * $buffset['badguydmgmod'], 0);
                    $selfdmg = max(0, round($selfdmg - ((int) $this->playerFunction->getPlayerPhysicalResistance()), 0));
                }
            }
        }

        // Handle god mode's invulnerability
        if ($buffset['invulnerable'])
        {
            $creaturedmg = abs($creaturedmg);
            $selfdmg     = -abs($selfdmg);
        }

        return [
            'creaturedmg' => ($creaturedmg ?? 0),
            'selfdmg'     => ($selfdmg ?? 0),
        ];
    }

    public function reportPowerMove($crit, $dmg)
    {
        global $session, $countround, $lotgdBattleContent;

        $uatk = $this->playerFunction->getPlayerAttack();

        if ($crit > $uatk)
        {
            $power = 0;

            if ($crit > $uatk * 4)
            {
                $msg   = 'skill.power.move.mega';
                $power = 1;
            }
            elseif ($crit > $uatk * 3)
            {
                $msg   = 'skill.power.move.double';
                $power = 1;
            }
            elseif ($crit > $uatk * 2)
            {
                $msg   = 'skill.power.move.power';
                $power = 1;
            }
            elseif ($crit > ($uatk * 1.5))
            {
                $msg   = 'skill.power.move.minor';
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

    public function isBuffActive($name)
    {
        global $session;
        // If it's not already suspended.
        return ($session['bufflist'][$name] && ! $session['bufflist'][$name]['suspended']) ? 1 : 0;
    }

    public function applyBodyguard($level): void
    {
        global $session;

        if ( ! isset($session['bufflist']['bodyguard']))
        {
            return;
        }

        switch ($level)
        {
            default:
            case 1:
                $badguyatkmod = 1.05;
                $defmod       = 0.95;
                $rounds       = -1;

            break;

            case 2:
                $badguyatkmod = 1.1;
                $defmod       = 0.9;
                $rounds       = -1;

            break;

            case 3:
                $badguyatkmod = 1.2;
                $defmod       = 0.8;
                $rounds       = -1;

            break;

            case 4:
                $badguyatkmod = 1.3;
                $defmod       = 0.7;
                $rounds       = -1;

            break;

            case 5:
                $badguyatkmod = 1.4;
                $defmod       = 0.6;
                $rounds       = -1;

            break;
        }

        $this->applyBuff('bodyguard', [
            'startmsg'         => $this->translator->trans('skill.bodyguard.startmsg', [], 'page_battle'),
            'name'             => $this->translator->trans('skill.bodyguard.name', [], 'page_battle'),
            'wearoff'          => $this->translator->trans('skill.bodyguard.wearoff', [], 'page_battle'),
            'badguyatkmod'     => $badguyatkmod,
            'defmod'           => $defmod,
            'rounds'           => $rounds,
            'allowinpvp'       => 1,
            'expireafterfight' => 1,
            'schema'           => 'pvp',
        ]);
    }

    public function applySkill($skill, $l)
    {
        if ('godmode' == $skill)
        {
            $this->applyBuff('godmode', [
                'name'         => $this->translator->trans('skill.godmode.name', [], 'page_battle'),
                'rounds'       => 1,
                'wearoff'      => $this->translator->trans('skill.godmode.wearoff', [], 'page_battle'),
                'atkmod'       => 25,
                'defmod'       => 25,
                'invulnerable' => 1,
                'startmsg'     => $this->translator->trans('skill.godmode.startmsg', [], 'page_battle'),
                'schema'       => 'skill',
            ]);
        }

        $this->dispatcher->dispatch(new Fight(), Fight::APPLY_SPECIALTY);
        modulehook('apply-specialties');
    }
}
