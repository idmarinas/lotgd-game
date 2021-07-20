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

trait Action
{
    /**
     * Battle: attack of player.
     *
     * @return bool
     */
    public function battlePlayerAttacks()
    {
        global $badguy, $enemies, $newenemies, $session, $creatureattack, $creatureatkmod, $beta;
        global $creaturedefmod, $adjustment, $defmod,$atkmod,$compatkmod, $compdefmod, $buffset, $atk, $def, $options;
        global $companions, $companion, $newcompanions, $roll, $count, $countround, $needtostopfighting, $lotgdBattleContent;

        $break       = false;
        $creaturedmg = $roll['creaturedmg'];

        if ('pvp' != $options['type'])
        {
            $creaturedmg = $this->reportPowerMove($atk, $creaturedmg);
        }

        if (0 == $creaturedmg)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                'combat.ally.miss', //-- Translator key
                [ //--- Params
                    'creatureName' => $badguy['creaturename'],
                ],
            ];
            $this->processDmgShield($buffset['dmgshield'], 0);
            $this->processLifeTaps($buffset['lifetap'], 0);
        }
        elseif ($creaturedmg < 0)
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                'combat.ally.riposted',
                [
                    'creatureName' => $badguy['creaturename'],
                    'damage'       => (0 - $creaturedmg),
                ],
            ];
            $badguy['diddamage'] = 1;
            $session['user']['hitpoints'] += $creaturedmg;

            if ($session['user']['hitpoints'] <= 0)
            {
                $badguy['killedplayer'] = true;
                $count                  = 1;
                $break                  = true;
                $needtostopfighting     = true;
            }
            $this->processDmgShield($buffset['dmgshield'], -$creaturedmg);
            $this->processLifeTaps($buffset['lifetap'], $creaturedmg);
        }
        else
        {
            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                'combat.ally.damage',
                [
                    'creatureName' => $badguy['creaturename'],
                    'damage'       => $creaturedmg,
                ],
            ];
            $badguy['creaturehealth'] -= $creaturedmg;
            $this->processDmgShield($buffset['dmgshield'], -$creaturedmg);
            $this->processLifeTaps($buffset['lifetap'], $creaturedmg);
        }

        if ($badguy['creaturehealth'] <= 0)
        {
            $badguy['dead']     = true;
            $badguy['istarget'] = false;
            $count              = 1;
            $break              = true;
        }

        return $break;
    }

    /**
     *  Battle: attack of badguy.
     *
     * @return bool
     */
    public function battleBadguyAttacks()
    {
        global $badguy, $enemies, $newenemies, $session, $creatureattack, $creatureatkmod, $beta;
        global $creaturedefmod, $adjustment, $defmod, $atkmod, $compatkmod, $compdefmod, $buffset, $atk, $def, $options;
        global $companions, $companion, $newcompanions, $roll, $countround, $index, $defended, $needtostopfighting, $lotgdBattleContent;

        $break   = false;
        $selfdmg = $roll['selfdmg'];

        if ($badguy['creaturehealth'] <= 0 && $session['user']['hitpoints'] <= 0)
        {
            $creaturedmg = 0;
            $selfdmg     = 0;

            if ($badguy['creaturehealth'] <= 0)
            {
                $badguy['dead']     = true;
                $badguy['istarget'] = false;
                $count              = 1;
                $needtostopfighting = true;
                $break              = true;
            }
            $newenemies[$index] = $badguy;
            $newcompanions      = $companions;
            $break              = true;
        }
        else
        {
            if ($badguy['creaturehealth'] > 0 && $session['user']['hitpoints'] > 0 && $badguy['istarget'])
            {
                if (\is_array($companions))
                {
                    foreach ($companions as $name => $companion)
                    {
                        if ($companion['hitpoints'] > 0)
                        {
                            $buffer = $this->reportCompanionMove($companion, 'defend');

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

            if ( ! $defended)
            {
                if (0 == $selfdmg)
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.enemy.miss', //-- Translator key
                        [ //-- Params
                            'creatureName' => $badguy['creaturename'],
                        ],
                    ];
                    $this->processDmgShield($buffset['dmgshield'], 0);
                    $this->processLifeTaps($buffset['lifetap'], 0);
                }
                elseif ($selfdmg < 0)
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.enemy.riposted',
                        [
                            'creatureName' => $badguy['creaturename'],
                            'damage'       => (0 - $selfdmg),
                        ],
                    ];
                    $badguy['creaturehealth'] += $selfdmg;
                    $this->processLifeTaps($buffset['lifetap'], -$selfdmg);
                    $this->processDmgShield($buffset['dmgshield'], $selfdmg);
                }
                else
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.enemy.damage',
                        [
                            'creatureName' => $badguy['creaturename'],
                            'damage'       => $selfdmg,
                        ],
                    ];
                    $session['user']['hitpoints'] -= $selfdmg;

                    if ($session['user']['hitpoints'] <= 0)
                    {
                        $badguy['killedplayer'] = true;
                        $count                  = 1;
                    }
                    $this->processDmgShield($buffset['dmgshield'], $selfdmg);
                    $this->processLifeTaps($buffset['lifetap'], -$selfdmg);
                    $badguy['diddamage'] = 1;
                }
            }

            if ($badguy['creaturehealth'] <= 0)
            {
                $badguy['dead']     = true;
                $badguy['istarget'] = false;
                $count              = 1;
                $break              = true;
            }
        }

        return $break;
    }
}
