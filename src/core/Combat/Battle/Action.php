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

    /**
     * Function to show result of victory battle.
     *
     * @param array $enemies
     * @param bool  $denyflawless
     * @param bool  $forest
     */
    public function battleVictory($enemies, $denyflawless = false, $forest = true)
    {
        global $session, $options, $lotgdBattleContent, $expbonus, $exp, $enemies, $deathoverlord;

        $diddamage     = false;
        $creaturelevel = 0;
        $gold          = 0;
        $exp           = 0;
        $expbonus      = 0;
        $count         = \count($enemies);

        \array_unshift($lotgdBattleContent['battleend'], '`n');

        foreach ($enemies as $index => $badguy)
        {
            $badguy['creaturegold'] = e_rand(0, $badguy['creaturegold']);

            if ($this->settings->getSetting('dropmingold', 0))
            {
                $badguy['creaturegold'] = e_rand(\round($badguy['creaturegold'] / 4), \round(3 * $badguy['creaturegold'] / 4));
            }

            $gold += $badguy['creaturegold'];

            if (isset($badguy['creaturelose']))
            {
                \array_unshift($lotgdBattleContent['battleend'], $this->tools->substitute($badguy['creaturelose'].'`n'));
            }

            if (true === $forest)
            {
                \array_unshift($lotgdBattleContent['battleend'], [
                    'combat.end.slain',
                    [
                        'creatureName' => $badguy['creaturename'],
                    ],
                ]);
            }
            elseif (false === $forest)
            {
                \array_unshift($lotgdBattleContent['battleend'], [
                    'combat.end.tormented',
                    [
                        'creatureName' => $badguy['creaturename'],
                    ],
                ]);
            }

            // If any creature did damage, we have no flawless fight. Easy as that.
            if (isset($badguy['diddamage']) && 1 == $badguy['diddamage'])
            {
                $diddamage = true;
            }
            $creaturelevel = \max($creaturelevel, $badguy['creaturelevel']);

            if ( ! $denyflawless && isset($badguy['denyflawless']) && $badguy['denyflawless'] > '')
            {
                $denyflawless = $badguy['denyflawless'];
            }

            $expbonus += \round(($badguy['creatureexp'] * (1 + .25 * ($badguy['creaturelevel'] - $session['user']['level']))) - $badguy['creatureexp'], 0);
        }

        $multibonus = $count > 1 ? 1 : 0;
        $expbonus += $session['user']['dragonkills'] * $session['user']['level'] * $multibonus;
        $totalexp = 0;

        foreach ($options['experience'] as $index => $experience)
        {
            $totalexp += $experience;
        }

        // We now have the total experience which should have been gained during the fight.
        // Now we will calculate the average exp per enemy.
        $exp      = \round($totalexp / $count);
        $gold     = e_rand(\round($gold / $count), \round(($gold / $count) * (($count + 1) * \pow(1.2, $count - 1)), 0));
        $expbonus = \round($expbonus / $count, 0);

        //-- No gem hunters allowed!
        $args = new Fight(['chance' => $this->settings->getSetting('forestgemchance', 25)]);
        $this->dispatcher->dispatch($args, Fight::ALTER_GEM_CHANCE);
        $args       = modulehook('alter-gemchance', $args->getData());
        $gemchances = $args['chance'];
        //-- Gems only find in forest
        if ($session['user']['level'] < $this->settings->getSetting('maxlevel', 15) && 1 == e_rand(1, $gemchances) && true === $forest)
        {
            $lotgdBattleContent['battleend'][] = ['combat.end.get.gem'];
            ++$session['user']['gems'];
            $this->log->debug('found gem when slaying a monster.', false, false, 'forestwingem', 1);
        }

        //-- Gold for user only in forest
        if ($gold && true === $forest)
        {
            $lotgdBattleContent['battleend'][] = [
                'combat.end.get.gold',
                [
                    'gold' => $gold,
                ],
            ];
            $session['user']['gold'] += $gold;
            $this->log->debug('received gold for slaying a monster.', false, false, 'forestwin', $badguy['creaturegold']);
        }

        //-- Process exp/favor
        if (true === $forest)
        {
            $this->battleGainExperienceForest();
        }
        elseif (false === $forest)
        {
            $this->battleGainExperienceGraveyard();
        }

        // Increase the level for each enemy by one half, so flawless fights can be achieved for
        // fighting multiple low-level critters
        if ( ! $creaturelevel)
        {
            $creaturelevel = $badguy['creaturelevel'];
        }
        else
        {
            $creaturelevel += (0.5 * ($count - 1));
        }

        //-- Perfect battle
        if ( ! $diddamage)
        {
            \array_push($lotgdBattleContent['battleend'], 'combat.end.flawless');

            if ($denyflawless)
            {
                \array_push($lotgdBattleContent['battleend'], "`c`\${$denyflawless}`0´c");
            }
            elseif ($session['user']['level'] <= $creaturelevel)
            {
                if (false === $forest)
                {//-- Only when is a Graveyard
                    \array_push($lotgdBattleContent['battleend'], 'combat.end.get.torment');
                    ++$session['user']['gravefights'];
                }
                //-- $forest === true or is other value
                else
                {
                    if (is_module_active('staminasystem'))
                    {
                        require_once 'modules/staminasystem/lib/lib.php';

                        \array_push($lotgdBattleContent['battleend'], 'combat.end.get.stamina');
                        addstamina(25000);
                    }
                    else
                    {
                        \array_push($lotgdBattleContent['battleend'], 'combat.end.get.turn');
                        ++$session['user']['turns'];
                    }
                }
            }
            else
            {
                if (is_module_active('staminasystem') && true === $forest)
                {
                    \array_push($lotgdBattleContent['battleend'], 'combat.end.forget.stamina');
                }
                elseif (false === $forest)
                {
                    \array_push($lotgdBattleContent['battleend'], 'combat.end.forget.torment');
                }
                else
                {
                    \array_push($lotgdBattleContent['battleend'], '');
                }
            }
        }

        if ($session['user']['hitpoints'] <= 0)
        {
            \array_push($lotgdBattleContent['battleend'], 'combat.end.negative.hitpoints');
            $session['user']['hitpoints'] = 1;
        }
    }

    /**
     * Process win experiencie in battle win in forest.
     */
    public function battleGainExperienceForest()
    {
        global $lotgdBattleContent, $options, $enemies, $session, $expbonus, $exp;

        $count = \count($enemies);

        if ($this->settings->getSetting('instantexp', false))
        {
            $expgained = 0;

            foreach ($options['experiencegained'] as $index => $experience)
            {
                $expgained += $experience;
            }

            $diff = $expgained - $exp;
            $expbonus += $diff;

            if (\floor($exp + $expbonus) < 0)
            {
                $expbonus = -$exp + 1;
            }

            if ($expbonus > 0)
            {
                $expbonus                          = \round($expbonus * \pow(1 + ($this->settings->getSetting('addexp', 5) / 100), $count - 1), 0);
                $lotgdBattleContent['battleend'][] = [
                    'combat.end.experience.forest.bonus',
                    [
                        'bonus' => $expbonus,
                    ],
                ];
            }
            elseif ($expbonus < 0)
            {
                $lotgdBattleContent['battleend'][] = [
                    'combat.end.experience.forest.penalize',
                    [
                        'bonus' => \abs($expbonus),
                    ],
                ];
            }

            if (\count($enemies) > 1)
            {
                $lotgdBattleContent['battleend'][] = [
                    'combat.end.experience.fores.instant.exp',
                    [
                        'experience' => $exp + $expbonus,
                    ],
                ];
            }
            $session['user']['experience'] += $expbonus;
        }
        else
        {
            if (\floor($exp + $expbonus) < 0)
            {
                $expbonus = -$exp + 1;
            }

            if ($expbonus > 0)
            {
                $expbonus                          = \round($expbonus * \pow(1 + ($this->settings->getSetting('addexp', 5) / 100), $count - 1), 0);
                $lotgdBattleContent['battleend'][] = [
                    'combat.end.experience.forest.bonus',
                    [
                        'bonus'     => $expbonus,
                        'calculate' => true,
                        'exp'       => $exp,
                        'totalExp'  => $exp + $expbonus,
                    ],
                ];
            }
            elseif ($expbonus < 0)
            {
                $lotgdBattleContent['battleend'][] = [
                    'combat.end.experience.forest.penalize',
                    [
                        'bonus'     => \abs($expbonus),
                        'calculate' => true,
                        'exp'       => $exp,
                        'totalExp'  => $exp + $expbonus,
                    ],
                ];
            }

            $totalExp = ($exp + $expbonus);
            //-- Only show if win Exp
            if ($totalExp)
            {
                $lotgdBattleContent['battleend'][] = [
                    'combat.end.experience.forest.total.exp',
                    [
                        'experience' => $totalExp,
                    ],
                ];
                $session['user']['experience'] += $totalExp;
            }
        }
    }

    /**
     * Process win experiencie in battle win in graveyard.
     */
    public function battleGainExperienceGraveyard()
    {
        global $lotgdBattleContent, $options, $session, $enemies, $expbonus, $exp;

        if (\floor($exp + $expbonus) < 0)
        {
            $expbonus = -$exp + 1;
        }

        $count = \count($enemies);

        if ($expbonus > 0)
        {
            $expbonus                          = \round($expbonus * \pow(1 + ($this->settings->getSetting('addexp', 5) / 100), $count - 1), 0);
            $lotgdBattleContent['battleend'][] = [
                'combat.end.experience.graveyard.bonus',
                [
                    'bonus'    => $expbonus,
                    'exp'      => $exp,
                    'totalExp' => $exp + $expbonus,
                ],
            ];
        }
        elseif ($expbonus < 0)
        {
            $lotgdBattleContent['battleend'][] = [
                'combat.end.experience.graveyard.penalize',
                [
                    'bonus'    => \abs($expbonus),
                    'exp'      => $exp,
                    'totalExp' => $exp + $expbonus,
                ],
            ];
        }

        $totalExp = ($exp + $expbonus);
        //-- Only show if win Exp/favor
        if ($totalExp)
        {
            $lotgdBattleContent['battleend'][] = [
                'combat.end.experience.graveyard.total.favor',
                [
                    'favor'              => $totalExp,
                    'graveyardOwnerName' => (string) $this->settings->getSetting('deathoverlord', '`$Ramius`0'),
                ],
            ];
            $session['user']['deathpower'] += $totalExp;
        }
    }

    /**
     * Function to show result of defeated battle.
     *
     * @param array        $enemies
     * @param string|false $where
     * @param bool         $candie   Can die in battle?
     * @param bool         $lostexp  Lost exp when die in battle?
     * @param bool         $lostgold Lost gold when die in battle?
     */
    public function battleDefeat($enemies, $where = 'forest', $candie = true, $lostexp = true, $lostgold = true)
    {
        global $session, $lotgdBattleContent;

        $percent = $this->settings->getSetting('forestexploss', 10);
        $killer  = false;

        foreach ($enemies as $index => $badguy)
        {
            if (isset($badguy['killedplayer']) && $badguy['killedplayer'])
            {
                $killer = $badguy;
            }

            if (isset($badguy['creaturewin']) && $badguy['creaturewin'] > '')
            {
                $lotgdBattleContent['battleend'][] = $this->tools->substitute("`b`&{$badguy['creaturewin']}`0´b`n");
            }
        }

        if ($killer)
        {
            $lotgdBattleContent['battleend'][] = [
                'combat.end.defeated.die',
                [
                    'creatureName' => $killer['creaturename'],
                ],
            ];
        }

        //-- If not want add a news when defeat set $where in null|''|false
        if ($where)
        {
            $deathmessage = $this->tools->selectDeathMessage($where);
            $taunt        = $this->tools->selectTaunt();

            $this->tools->addNews('deathmessage', [
                'deathmessage' => $deathmessage,
                'taunt'        => $taunt,
            ], '');
        }

        if ($lostgold)
        {
            $this->log->debug("lost gold when they were slain {$where}", false, false, 'forestlose', -$session['user']['gold']);
            $session['user']['gold'] = 0;

            $lotgdBattleContent['battleend'][] = 'combat.end.defeated.lost.gold';
        }

        if ($lostexp)
        {
            $session['user']['experience'] = \round($session['user']['experience'] * (1 - ($percent / 100)), 0);

            $lotgdBattleContent['battleend'][] = [
                'combat.end.defeated.lost.exp',
                [
                    'percent' => ($percent / 100),
                ],
            ];
        }

        if ($candie)
        {
            $this->navigation->addNav('battle.nav.news', 'news.php', ['textDomain' => 'navigation_app']);

            $session['user']['alive']          = false;
            $session['user']['hitpoints']      = 0;
            $lotgdBattleContent['battleend'][] = 'combat.end.defeated.tomorrow.forest';
        }
        elseif ('graveyard' === $where)
        {
            $this->navigation->addNav('battle.nav.graveyard', 'graveyard.php');

            $session['user']['gravefights']    = 0;
            $lotgdBattleContent['battleend'][] = 'combat.end.defeated.tomorrow.graveyard';
        }
    }

    /**
     * Show result of.
     */
    public function battleShowResults(array $lotgdBattleContent)
    {
        $this->response->pageAddContent($this->twig->render('page/battle.html.twig', $lotgdBattleContent));
    }
}
