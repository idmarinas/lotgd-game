<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

trait Formula
{
    protected function rollDamage($badguy)
    {
        $creaturedmg = 0;
        $selfdmg     = 0;

        // // $creaturedefmod = $buffset['badguydefmod'];
        // // $creatureatkmod = $buffset['badguyatkmod'];
        // // $atkmod = $buffset['atkmod'];
        // // $defmod = $buffset['defmod'];
        // // $compatkmod = $buffset['compatkmod'];
        // // $compdefmod = $buffset['compdefmod'];

        if ($this->isEnemyAlive($badguy) && $this->isPlayerAlive())
        {
            $adjustedcreaturedefense = $badguy['creaturedefense'];

            if ('pvp' != $this->getOptionType())
            {
                $adjustedcreaturedefense = ($this->buffModifiers['badguydefmod'] * $badguy['creaturedefense']);
            }

            $creatureattack      = $badguy['creatureattack'] * $this->buffModifiers['badguyatkmod'];
            $adjustedselfdefense = ($this->playerFunction->getPlayerDefense() * $this->buffModifiers['defmod']);

            if ( ! isset($badguy['physicalresistance']))
            {
                $badguy['physicalresistance'] = 0;
            }
            $powerattack      = (int) $this->settings->getSetting('forestpowerattackchance', 10);
            $powerattackmulti = (float) $this->settings->getSetting('forestpowerattackmulti', 3);

            while (0 == $creaturedmg && 0 == $selfdmg)
            {
                $atk = $this->playerFunction->getPlayerAttack() * $this->buffModifiers['atkmod'];

                if (1 == mt_rand(1, 20) && 'pvp' != $this->getOptionType())
                {
                    $atk *= 3;
                }

                $patkroll = bell_rand(0, $atk);

                // Set up for crit detection
                $atk      = $patkroll;
                $catkroll = bell_rand(0, $adjustedcreaturedefense);

                $creaturedmg = -(int) ($catkroll - $patkroll);

                if ($creaturedmg < 0)
                {
                    $creaturedmg = (int) ($creaturedmg / 2);
                    $creaturedmg = round($this->buffModifiers['badguydmgmod'] * $creaturedmg, 0);
                    $creaturedmg = min(0, round($creaturedmg - $badguy['physicalresistance']));
                }

                if ($creaturedmg > 0)
                {
                    $creaturedmg = round($this->buffModifiers['dmgmod'] * $creaturedmg, 0);
                    $creaturedmg = max(0, round($creaturedmg - $badguy['physicalresistance']));
                }
                $pdefroll = bell_rand(0, $adjustedselfdefense);
                $catkroll = bell_rand(0, $creatureattack);

                if (0 != $powerattack && 'pvp' != $this->getOptionType() && 1 == e_rand(1, $powerattack))
                {
                    $catkroll *= $powerattackmulti;
                }

                $selfdmg = -(int) ($pdefroll - $catkroll);

                if ($selfdmg < 0)
                {
                    $selfdmg = (int) ($selfdmg / 2);
                    $selfdmg = round($selfdmg * $this->buffModifiers['dmgmod'], 0);
                    $selfdmg = min(0, round($selfdmg - ((int) $this->playerFunction->getPlayerPhysicalResistance()), 0));
                }

                if ($selfdmg > 0)
                {
                    $selfdmg = round($selfdmg * $this->buffModifiers['badguydmgmod'], 0);
                    $selfdmg = max(0, round($selfdmg - ((int) $this->playerFunction->getPlayerPhysicalResistance()), 0));
                }
            }
        }

        // Handle god mode's invulnerability
        if ($this->buffModifiers['invulnerable'])
        {
            $creaturedmg = abs($creaturedmg);
            $selfdmg     = -abs($selfdmg);
        }

        return [
            'creaturedmg' => $creaturedmg ?? 0,
            'selfdmg'     => $selfdmg ?? 0,
            'atk'         => $atk,
        ];
    }

    /**
     * Based upon the companion's stats damage values are calculated.
     *
     * @param array $companion
     * @param array $badguy
     *
     * @return array
     */
    protected function rollCompanionDamage($companion, $badguy)
    {
        $creaturedmg = 0;
        $selfdmg     = 0;

        if ($badguy['creaturehealth'] > 0 && $companion['hitpoints'] > 0)
        {
            $adjustedcreaturedefense = ($this->buffModifiers['badguydefmod'] * $badguy['creaturedefense']);

            if ('pvp' == $this->getOptionType())
            {
                $adjustedcreaturedefense = $badguy['creaturedefense'];
            }

            $creatureattack      = $badguy['creatureattack'] * $this->buffModifiers['badguyatkmod'];
            $adjustedselfdefense = ($companion['defense'] * $this->buffModifiers['compdefmod']);

            while (0 == $creaturedmg && 0 == $selfdmg)
            {
                $atk = $companion['attack'] * $this->buffModifiers['compatkmod'];

                if (1 == mt_rand(1, 20) && 'pvp' != $this->getOptionType())
                {
                    $atk *= 3;
                }

                $patkroll = bell_rand(0, $atk);
                // Set up for crit detection
                $atk      = $patkroll;
                $catkroll = bell_rand(0, $adjustedcreaturedefense);

                $creaturedmg = -(int) ($catkroll - $patkroll);

                if ($creaturedmg < 0)
                {
                    $creaturedmg = (int) ($creaturedmg / 2);
                    $creaturedmg = round($this->buffModifiers['badguydmgmod'] * $creaturedmg, 0);
                }

                if ($creaturedmg > 0)
                {
                    $creaturedmg = round($this->buffModifiers['compdmgmod'] * $creaturedmg, 0);
                }

                $pdefroll = bell_rand(0, $adjustedselfdefense);
                $catkroll = bell_rand(0, $creatureattack);

                $selfdmg = -(int) ($pdefroll - $catkroll);

                if ($selfdmg < 0)
                {
                    $selfdmg = (int) ($selfdmg / 2);
                    $selfdmg = round($selfdmg * $this->buffModifiers['compdmgmod'], 0);
                }

                if ($selfdmg > 0)
                {
                    $selfdmg = round($selfdmg * $this->buffModifiers['badguydmgmod'], 0);
                }
            }
        }

        // Handle god mode's invulnerability
        if ($this->buffModifiers['invulnerable'])
        {
            $creaturedmg = abs($creaturedmg);
            $selfdmg     = -abs($selfdmg);
        }

        return [
            'creaturedmg' => ($creaturedmg ?? 0),
            'selfdmg'     => ($selfdmg ?? 0),
            'atk'         => $atk,
        ];
    }
}
