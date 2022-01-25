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

trait Movement
{
    protected $defended = false;

    /**
     * Movement of Healer companion.
     *
     * @param array $badguy
     */
    protected function companionHealer(&$badguy): void
    {
        foreach ($this->companions as &$companion)
        {
            if ($companion['hitpoints'] > 0)
            {
                $this->reportCompanionMove($companion, $badguy, 'heal');
            }
        }
    }

    /**
     * Movement of Magic Companion.
     *
     * @param array $badguy
     */
    protected function companionMagic(&$badguy): void
    {
        $this->buffModifiers = $this->activateBuffs('offense', $badguy);

        foreach ($this->companions as &$companion)
        {
            if ($companion['hitpoints'] > 0)
            {
                $this->reportCompanionMove($companion, $badguy, 'magic');
            }
        }
    }

    /**
     * Movement of Defender Companion.
     *
     * @param array $badguy
     */
    protected function companionDefender(&$badguy): void
    {
        foreach ($this->companions as &$companion)
        {
            if ($companion['hitpoints'] > 0)
            {
                $this->reportCompanionMove($companion, $badguy, 'defend');
            }
        }
    }

    /**
     * Movement of Fighter Companion.
     *
     * @param array $badguy
     */
    protected function companionFighter(&$badguy): void
    {
        foreach ($this->companions as &$companion)
        {
            if ($companion['hitpoints'] > 0)
            {
                $this->reportCompanionMove($companion, $badguy, 'fight');
            }
        }
    }

    protected function playerMove(&$badguy): void
    {
        $ggchancetodouble = $this->user['dragonkills'];

        do
        {
            $additionalattack = false;
            //-- Process move of player
            if ($this->isEnemyAlive($badguy) && $this->isPlayerAlive())
            {
                $this->reportPlayerMove($badguy);
            }

            $r = mt_rand(0, 100);

            if ($r < $ggchancetodouble && $this->isEnemyAlive($badguy) && $this->isPlayerAlive())
            {
                $additionalattack = true;
                $ggchancetodouble -= ($r + 5);
            }
        } while ($additionalattack && $this->isEnemyAlive($badguy) && $this->isPlayerAlive());
    }

    protected function enemyMove(&$badguy): void
    {
        $this->buffModifiers = $this->activateBuffs('defense', $badguy);

        do
        {
            $additionalattack = false;
            $bgchancetodouble = $this->user['dragonkills'];
            $this->defended   = false;

            if ($this->isEnemyAlive($badguy) && $this->isPlayerAlive())
            {
                $this->reportEnemyMove($badguy);
            }

            $r = mt_rand(0, 100);

            if ($r < $bgchancetodouble && $this->isEnemyAlive($badguy) && $this->isPlayerAlive())
            {
                $additionalattack = true;
                $bgchancetodouble -= ($r + 5);
            }
        } while ($additionalattack && ! $this->defended && $this->isEnemyAlive($badguy) && $this->isPlayerAlive());
    }

    /**
     *  Battle: attack of badguy.
     *
     * @param mixed $badguy
     *
     * @return bool
     */
    protected function reportEnemyMove(&$badguy): void
    {
        if ($this->defended || ! $this->isEnemyAlive($badguy) || ! $this->isPlayerAlive())
        {
            return;
        }

        $roll    = $this->rollDamage($badguy);
        $selfdmg = $roll['selfdmg'];

        //-- First defenders companions
        if ( ! $this->isEnemyAlive($badguy) && ! ($this->isPlayerAlive() && $badguy['istarget']))
        {
            $this->companionDefender($badguy);
        }

        if (0 == $selfdmg)
        {
            $this->addContextToRoundEnemy([
                'combat.enemy.miss', //-- Translator key
                [ //--- Params
                    'creatureName' => $badguy['creaturename'],
                ],
                $this->getTranslationDomain(),
            ]);
            $this->processDmgshield($this->buffModifiers['dmgshield'], 0, $badguy);
            $this->processLifetaps($this->buffModifiers['lifetap'], 0, $badguy);
        }
        elseif ($selfdmg < 0)
        {
            $this->addContextToRoundEnemy([
                'combat.enemy.riposted', //-- Translator key
                [ //--- Params
                    'creatureName' => $badguy['creaturename'],
                    'damage'       => abs($selfdmg),
                ],
                $this->getTranslationDomain(),
            ]);
            $badguy['creaturehealth'] += $selfdmg;
            $this->processLifetaps($this->buffModifiers['lifetap'], -$selfdmg, $badguy);
            $this->processDmgshield($this->buffModifiers['dmgshield'], $selfdmg, $badguy);
        }
        else
        {
            $this->addContextToRoundEnemy([
                'combat.enemy.damage', //-- Translator key
                [ //--- Params
                    'creatureName' => $badguy['creaturename'],
                    'damage'       => $selfdmg,
                ],
                $this->getTranslationDomain(),
            ]);
            $this->user['hitpoints'] -= $selfdmg;

            if ( ! $this->isPlayerAlive())
            {
                $badguy['killedplayer'] = true;
            }
            $this->processDmgshield($this->buffModifiers['dmgshield'], $selfdmg, $badguy);
            $this->processLifetaps($this->buffModifiers['lifetap'], -$selfdmg, $badguy);
            $badguy['diddamage'] = 1;
        }
    }

    /**
     * Battle: attack of player.
     *
     * @param array $badguy
     *
     * @return bool
     */
    protected function reportPlayerMove(&$badguy): void
    {
        $roll        = $this->rollDamage($badguy);
        $creaturedmg = $roll['creaturedmg'];

        if ('pvp' != $this->getOptionType())
        {
            $creaturedmg = $this->reportPowerMove($roll['atk'], $creaturedmg);
        }

        if (0 == $creaturedmg)
        {
            $this->addContextToRoundAlly([
                'combat.ally.miss', //-- Translator key
                [ //--- Params
                    'creatureName' => $badguy['creaturename'],
                ],
                $this->getTranslationDomain(),
            ]);
        }
        elseif ($creaturedmg < 0)
        {
            $this->addContextToRoundAlly([
                'combat.ally.riposted', //-- Translator key
                [
                    'creatureName' => $badguy['creaturename'],
                    'damage'       => abs($creaturedmg),
                ],
                $this->getTranslationDomain(),
            ]);
            $badguy['diddamage'] = 1;
            $this->user['hitpoints'] += $creaturedmg;

            if ( ! $this->isPlayerAlive())
            {
                $badguy['killedplayer'] = true;
            }
        }
        else
        {
            $this->addContextToRoundAlly([
                'combat.ally.damage',
                [
                    'creatureName' => $badguy['creaturename'],
                    'damage'       => $creaturedmg,
                ],
                $this->getTranslationDomain(),
            ]);
            $badguy['creaturehealth'] -= $creaturedmg;
        }

        $this->processDmgshield($this->buffModifiers['dmgshield'], -$creaturedmg, $badguy);
        $this->processLifetaps($this->buffModifiers['lifetap'], $creaturedmg, $badguy);

        if ( ! $this->isEnemyAlive($badguy))
        {
            $badguy['dead']     = true;
            $badguy['istarget'] = false;
        }
    }

    /**
     * Based upon the type of the companion different actions are performed and the companion is marked as "used" after that.
     *
     * @param array  $companion The companion itself
     * @param string $activate  The stage of activation. Can be one of these: "fight", "defend", "heal" or "magic".
     * @param mixed  $badguy
     *
     * @return array The changed companion
     */
    protected function reportCompanionMove(&$companion, &$badguy, $activate = 'fight'): void
    {
        if ($companion['suspended'] ?? false)
        {
            return;
        }

        if ('fight' == $activate && ($companion['abilities']['fight'] ?? false) && ! $companion['used'])
        {
            $roll = $this->rollCompanionDamage($companion, $badguy);

            $damage_done     = $roll['creaturedmg'];
            $damage_received = $roll['selfdmg'];

            if (0 == $damage_done)
            {
                $this->addContextToRoundAlly([
                    'combat.companion.fight.attack.miss',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                    ],
                    $this->getTranslationDomain(),
                ]);
            }
            elseif ($damage_done < 0)
            {
                $this->addContextToRoundAlly([
                    'combat.companion.fight.attack.riposted',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => abs($damage_done),
                    ],
                    $this->getTranslationDomain(),
                ]);
                $companion['hitpoints'] += $damage_done;
            }
            else
            {
                $this->addContextToRoundAlly([
                    'combat.companion.fight.attack.damage',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => $damage_done,
                    ],
                    $this->getTranslationDomain(),
                ]);
                $badguy['creaturehealth'] -= $damage_done;
            }

            //-- Badguy
            if (0 == $damage_received)
            {
                $this->addContextToRoundEnemy([
                    'combat.companion.fight.defend.miss',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                    ],
                    $this->getTranslationDomain(),
                ]);
            }
            elseif ($damage_received < 0)
            {
                $this->addContextToRoundEnemy([
                    'combat.companion.fight.defend.riposted',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                        'damage'        => abs($damage_received),
                    ],
                    $this->getTranslationDomain(),
                ]);
                $badguy['creaturehealth'] += $damage_received;
            }
            else
            {
                $this->addContextToRoundEnemy([
                    'combat.companion.fight.defend.damage',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                        'damage'        => $damage_received,
                    ],
                    $this->getTranslationDomain(),
                ]);
                $companion['hitpoints'] -= $damage_received;
            }

            $companion['used'] = true;
        }
        elseif ('heal' == $activate && ($companion['abilities']['heal'] ?? false) && ! $companion['used'])
        {
            // This one will be tricky! We are looking for the first target which can be healed. This can be the player himself
            // or any other companion or our fellow companion himself.
            if ($this->user['hitpoints'] < $this->user['maxhitpoints'])
            {
                $hptoheal = min($companion['abilities']['heal'], $this->user['maxhitpoints'] - $this->user['hitpoints']);
                $this->user['hitpoints'] += $hptoheal;
                $companion['used'] = true;

                $this->addContextToRoundAlly([
                    $companion['healmsg'] ?? 'combat.companion.heal.player',
                    [
                        'companionName' => $companion['name'],
                        'damage'        => $hptoheal,
                    ],
                    $this->getTranslationDomain(),
                ]);
            }
            else
            {
                $healed = false;

                foreach ($this->companions as $name => $otherCompanion)
                {
                    if (
                        $name == $companion['name']
                        || $otherCompanion['hitpoints'] >= $otherCompanion['maxhitpoints']
                        || $healed
                        || ($companion['cannotbehealed'] ?? false)
                    ) {
                        continue;
                    }

                    $hptoheal = min($companion['abilities']['heal'], $otherCompanion['maxhitpoints'] - $otherCompanion['hitpoints']);
                    $otherCompanion['hitpoints'] += $hptoheal;
                    $companion['used'] = true;

                    $this->addContextToRoundAlly([
                        $companion['healcompanionmsg'] ?? 'combat.companion.heal.companion',
                        [
                            'companionName' => $companion['name'],
                            'damage'        => $hptoheal,
                            'target'        => $otherCompanion['name'],
                        ],
                        $this->getTranslationDomain(),
                    ]);

                    $healed = true;
                }
            }

            $roll            = $this->rollCompanionDamage($companion, $badguy);
            $damage_received = $roll['selfdmg'];

            if (0 == $damage_received)
            {
                $this->addContextToRoundAlly([
                    'combat.companion.heal.defend.miss',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                    ],
                    $this->getTranslationDomain(),
                ]);
            }
            elseif ($damage_received < 0)
            {
                $this->addContextToRoundAlly([
                    'combat.companion.heal.defend.riposted',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                        'damage'        => abs($damage_received),
                    ],
                    $this->getTranslationDomain(),
                ]);

                $badguy['creaturehealth'] += $damage_received;
            }
            else
            {
                $this->addContextToRoundAlly([
                    'combat.companion.heal.defend.damage',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                        'damage'        => $damage_received,
                    ],
                    $this->getTranslationDomain(),
                ]);

                $companion['hitpoints'] -= $damage_received;
            }
            $companion['used'] = true;
        }
        elseif ('defend' == $activate && ($companion['abilities']['defend'] ?? false) && ! $this->defended && ! $companion['used'])
        {
            $this->defended  = true;
            $roll            = $this->rollCompanionDamage($companion, $badguy);
            $damage_done     = $roll['creaturedmg'];
            $damage_received = $roll['selfdmg'];

            if (0 == $damage_done)
            {
                $this->addContextToRoundAlly([
                    'comabat.companion.defend.attack.miss',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                    ],
                    $this->getTranslationDomain(),
                ]);
            }
            elseif ($damage_done < 0)
            {
                $this->addContextToRoundAlly([
                    'comabat.companion.defend.attack.riposted',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => abs($damage_done),
                    ],
                    $this->getTranslationDomain(),
                ]);

                $companion['hitpoints'] += $damage_done;
            }
            else
            {
                $this->addContextToRoundAlly([
                    'comabat.companion.defend.attack.damage',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => $damage_done,
                    ],
                    $this->getTranslationDomain(),
                ]);

                $badguy['creaturehealth'] -= $damage_done;
            }

            if (0 == $damage_received)
            {
                $this->addContextToRoundEnemy([
                    'combat.companion.defend.defend.miss',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                    ],
                    $this->getTranslationDomain(),
                ]);
            }
            elseif ($damage_received < 0)
            {
                $this->addContextToRoundEnemy([
                    'combat.companion.defend.defend.riposted',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                        'damage'        => abs($damage_received),
                    ],
                    $this->getTranslationDomain(),
                ]);

                $badguy['creaturehealth'] += $damage_received;
            }
            else
            {
                $this->addContextToRoundEnemy([
                    'combat.companion.defend.defend.damage',
                    [
                        'creatureName'  => $badguy['creaturename'],
                        'companionName' => $companion['name'],
                        'damage'        => $damage_received,
                    ],
                    $this->getTranslationDomain(),
                ]);
                $companion['hitpoints'] -= $damage_received;
            }

            $companion['used'] = true;
        }
        elseif ('magic' == $activate && ($companion['abilities']['magic'] ?? false) && ! $companion['used'])
        {
            $roll        = $this->rollCompanionDamage($companion);
            $damage_done = abs($roll['creaturedmg']);

            $msg = $companion['magicmsg'] ?? 'combat.companion.magic.damage';
            $badguy['creaturehealth'] -= $damage_done;
            if (0 == $damage_done)
            {
                $msg = $companion['magicfailmsg'] ?? 'combat.companion.magic.miss';
            }

            $this->addContextToRoundAlly([
                $msg,
                [
                    'companionName' => $companion['name'],
                    'creatureName'  => $badguy['creaturename'],
                    'damage'        => $damage_done,
                ],
                $this->getTranslationDomain(),
            ]);

            $companion['hitpoints'] -= $companion['abilities']['magic'];
            $companion['used'] = true;
        }

        $this->isEnemyAlive($badguy);
        //-- Delete companion if is dead and can delete delete companion
        if ( ! $this->isCompanionAlive($companion) && ! ($companion['cannotdie'] ?? false))
        {
            unset($companion);
        }
    }

    protected function reportPowerMove($crit, $dmg)
    {
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
                $this->addContextToRoundAlly($msg);

                $dmg += e_rand($crit / 4, $crit / 2);
                $dmg = max($dmg, 1);
            }
        }

        return $dmg;
    }

    /**
     * Executes the given script or loads the script and then executes it.
     */
    protected function enemyAiScript(array &$badguy)
    {
        if ( ! $badguy['dead'] && isset($badguy['creatureaiscript']) && $badguy['creatureaiscript'])
        {
            eval($badguy['creatureaiscript']);
        }
    }
}
