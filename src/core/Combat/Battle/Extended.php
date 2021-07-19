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

trait Extended
{
    /**
     * Based upon the type of the companion different actions are performed and the companion is marked as "used" after that.
     *
     * @param array  $companion The companion itself
     * @param string $activate  The stage of activation. Can be one of these: "fight", "defend", "heal" or "magic".
     *
     * @return array The changed companion
     */
    public function reportCompanionMove($companion, $activate = 'fight')
    {
        global $badguy, $session, $creatureattack, $creatureatkmod, $adjustment;
        global $creaturedefmod, $defmod, $atkmod, $atk, $def, $count, $countround, $defended, $needtosstopfighting, $lotgdBattleContent;

        if (isset($companion['suspended']) && true == $companion['suspended'])
        {
            return $companion;
        }

        if ('fight' == $activate && (bool) ($companion['abilities']['fight'] ?? false) && ! $companion['used'])
        {
            $roll = $this->rollCompanionDamage($companion);

            $damage_done     = $roll['creaturedmg'];
            $damage_received = $roll['selfdmg'];

            if (0 == $damage_done)
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                    'combat.companion.fight.attack.miss',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                    ],
                ];
            }
            elseif ($damage_done < 0)
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                    'combat.companion.fight.attack.riposted',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => \abs($damage_done),
                    ],
                ];
                $companion['hitpoints'] += $damage_done;
            }
            else
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                    'combat.companion.fight.attack.damage',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => $damage_done,
                    ],
                ];
                $badguy['creaturehealth'] -= $damage_done;
            }

            if ($badguy['creaturehealth'] >= 0)
            {
                if (0 == $damage_received)
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.fight.defend.miss',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                        ],
                    ];
                }
                elseif ($damage_received < 0)
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.fight.defend.riposted',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                            'damage'        => \abs($damage_received),
                        ],
                    ];
                    $badguy['creaturehealth'] += $damage_received;
                }
                else
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.fight.defend.damage',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                            'damage'        => $damage_received,
                        ],
                    ];
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
                $hptoheal = \min($companion['abilities']['heal'], $session['user']['maxhitpoints'] - $session['user']['hitpoints']);
                $session['user']['hitpoints'] += $hptoheal;
                $companion['used'] = true;
                $msg               = $companion['healmsg'] ?? '';

                $msg = [
                    $msg ?: 'combat.companion.heal.player',
                    [
                        'companionName' => $companion['name'],
                        'damage'        => $hptoheal,
                    ],
                ];

                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
            }
            else
            {
                // Okay. We really have to do this :(
                global $newcompanions;

                $mynewcompanions = $newcompanions;

                if ( ! \is_array($mynewcompanions))
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

                    $hptoheal = \min($companion['abilities']['heal'], $mycompanion['maxhitpoints'] - $mycompanion['hitpoints']);
                    $mycompanion['hitpoints'] += $hptoheal;
                    $companion['used'] = true;
                    $msg               = $companion['healcompanionmsg'] ?? '';

                    $msg = [
                        $msg ?: 'combat.companion.heal.companion',
                        [
                            'companionName' => $companion['name'],
                            'damage'        => $hptoheal,
                            'target'        => $mycompanion['name'],
                        ],
                    ];

                    $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
                    $healed                                                      = true;
                    $newcompanions[$myname]                                      = $mycompanion;
                }

                if ( ! $healed)
                {
                    global $companions, $name;

                    $mycompanions = $companions;
                    $foundmyself  = false;

                    foreach ($mycompanions as $myname => $mycompanion)
                    {
                        if ( ! $foundmyself || (isset($companion['cannotbehealed']) && true == $companion['cannotbehealed']))
                        {
                            if ($myname == $name)
                            {
                                $foundmyself = true;
                            }

                            continue;
                        }

                        //There's someone hiding behind us...
                        foreach ($mycompanions as $myname => $mycompanion)
                        {
                            if ($mycompanion['hitpoints'] >= $mycompanion['maxhitpoints'] || $healed)
                            {
                                continue;
                            }

                            $hptoheal = \min($companion['abilities']['heal'], $mycompanion['maxhitpoints'] - $mycompanion['hitpoints']);
                            $mycompanion['hitpoints'] += $hptoheal;
                            $companion['used'] = true;
                            $msg               = $companion['healcompanionmsg'] ?? '';

                            $msg = [
                                $msg ?: 'combat.companion.heal.companion',
                                [
                                    'companionName' => $companion['name'],
                                    'damage'        => $hptoheal,
                                    'target'        => $mycompanion['name'],
                                ],
                            ];

                            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;

                            $healed              = true;
                            $companions[$myname] = $mycompanion;
                        } // foreach
                    } // foreach
                } // if
            } // else
            unset($mynewcompanions, $mycompanions);

            $roll            = $this->rollCompanionDamage($companion);
            $damage_received = $roll['selfdmg'];

            if ($badguy['creaturehealth'] >= 0)
            {
                if (0 == $damage_received)
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.heal.defend.miss',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                        ],
                    ];
                }
                elseif ($damage_received < 0)
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.heal.defend.riposted',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                            'damage'        => \abs($damage_received),
                        ],
                    ];

                    $badguy['creaturehealth'] += $damage_received;
                }
                else
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.heal.defend.damage',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                            'damage'        => $damage_received,
                        ],
                    ];

                    $companion['hitpoints'] -= $damage_received;
                }
            }
            $companion['used'] = true;
        }
        elseif ('defend' == $activate && isset($companion['abilities']['defend']) && $companion['abilities']['defend'] && ! $defended && ! $companion['used'])
        {
            $defended        = 1;
            $roll            = $this->rollCompanionDamage($companion);
            $damage_done     = $roll['creaturedmg'];
            $damage_received = $roll['selfdmg'];

            if (0 == $damage_done)
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                    'comabat.companion.defend.attack.miss',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                    ],
                ];
            }
            elseif ($damage_done < 0)
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                    'combat.companion.defend.attack.riposted',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => \abs($damage_done),
                    ],
                ];

                $companion['hitpoints'] += $damage_done;
            }
            else
            {
                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = [
                    'combat.companion.defend.attack.damage',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => $damage_done,
                    ],
                ];

                $badguy['creaturehealth'] -= $damage_done;
            }

            if ($badguy['creaturehealth'] >= 0)
            {
                if (0 == $damage_received)
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.defend.defend.miss',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                        ],
                    ];
                }
                elseif ($damage_received < 0)
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.defend.defend.riposted',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                            'damage'        => \abs($damage_received),
                        ],
                    ];

                    $badguy['creaturehealth'] += $damage_received;
                }
                else
                {
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.companion.defend.defend.damage',
                        [
                            'creatureName'  => $badguy['creaturename'],
                            'companionName' => $companion['name'],
                            'damage'        => $damage_received,
                        ],
                    ];
                    $companion['hitpoints'] -= $damage_received;
                }
            }

            $companion['used'] = true;
        }
        elseif ('magic' == $activate && isset($companion['abilities']['magic']) && true == $companion['abilities']['magic'] && false == $companion['used'])
        {
            $roll        = $this->rollCompanionDamage($companion);
            $damage_done = \abs($roll['creaturedmg']);

            if (0 == $damage_done)
            {
                $msg = $companion['magicfailmsg'] ?? '';

                $msg = [
                    $msg ?: 'combat.companion.magic.miss',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                    ],
                ];

                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;
            }
            else
            {
                $msg = $companion['magicmsg'] ?? '';

                $msg = [
                    $msg ?: 'combat.companion.magic.damage',
                    [
                        'companionName' => $companion['name'],
                        'creatureName'  => $badguy['creaturename'],
                        'damage'        => $damage_done,
                    ],
                ];

                $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $msg;

                $badguy['creaturehealth'] -= $damage_done;
            }

            $companion['hitpoints'] -= $companion['abilities']['magic'];
            $companion['used'] = true;
        }

        if ($badguy['creaturehealth'] <= 0)
        {
            $badguy['dead']      = true;
            $badguy['istarget']  = false;
            $count               = 1;
            $needtosstopfighting = true;
        }

        if ($companion['hitpoints'] <= 0)
        {
            $msg = 'combat.companion.die';

            if (isset($companion['dyingtext']) && $companion['dyingtext'] > '')
            {
                $msg = $companion['dyingtext'];
            }

            $lotgdBattleContent['battlerounds'][$countround]['allied'][] = $this->tools->substituteArray("`){$msg}`0`n", ['{companion}'], [$companion['name']]);

            if (isset($companion['cannotdie']) && $companion['cannotdie'])
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
    public function rollCompanionDamage($companion)
    {
        global $badguy,$creatureattack, $creatureatkmod,$adjustment,$options;
        global $creaturedefmod,$compdefmod,$compatkmod,$buffset,$atk,$def;

        $creaturedmg = 0;
        $selfdmg     = 0;

        if ($badguy['creaturehealth'] > 0 && $companion['hitpoints'] > 0)
        {
            $adjustedcreaturedefense = ($creaturedefmod * $badguy['creaturedefense'] / ($adjustment * $adjustment));

            if ('pvp' == $options['type'])
            {
                $adjustedcreaturedefense = $badguy['creaturedefense'];
            }

            $creatureattack      = $badguy['creatureattack'] * $creatureatkmod;
            $adjustedselfdefense = ($companion['defense'] * $adjustment * $compdefmod);

            while ( ! isset($creaturedmg) || ! isset($selfdmg) || 0 == $creaturedmg && 0 == $selfdmg)
            {
                $atk = $companion['attack'] * $compatkmod;

                if (1 == e_rand(1, 20) && 'pvp' != $options['type'])
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
                    $creaturedmg = \round($buffset['badguydmgmod'] * $creaturedmg, 0);
                }
                elseif ($creaturedmg > 0)
                {
                    $creaturedmg = \round($buffset['compdmgmod'] * $creaturedmg, 0);
                }

                $pdefroll = bell_rand(0, $adjustedselfdefense);
                $catkroll = bell_rand(0, $creatureattack);
                $selfdmg = 0 - (int) ($pdefroll - $catkroll);

                if ($selfdmg < 0)
                {
                    $selfdmg = (int) ($selfdmg / 2);
                    $selfdmg = \round($selfdmg * $buffset['compdmgmod'], 0);
                }
                elseif ($selfdmg > 0)
                {
                    $selfdmg = \round($selfdmg * $buffset['badguydmgmod'], 0);
                }
            }
        }

        // Handle god mode's invulnerability
        if ($buffset['invulnerable'])
        {
            $creaturedmg = \abs($creaturedmg);
            $selfdmg     = -\abs($selfdmg);
        }

        return ['creaturedmg' => ($creaturedmg ?? 0), 'selfdmg' => ($selfdmg ?? 0)];
    }

    /**
     * Adds a new creature to the badguy array.
     *
     * @param mixed $creature A standard badguy array. If numeric, the corresponding badguy will be loaded from the database.
     */
    public function battleSpawn($creature)
    {
        global $enemies, $newenemies, $badguy, $nextindex, $countround, $lotgdBattleContent;

        if ( ! \is_array($newenemies))
        {
            $newenemies = [];
        }

        if ( ! isset($nextindex))
        {
            $nextindex = \count($enemies);
        }
        else
        {
            ++$nextindex;
        }

        if (\is_numeric($creature))
        {
            $repository = $this->doctrine->getRepository('LotgdCore:Creatures');
            $entity     = $repository->find($creature);

            if ($entity)
            {
                $newenemies[$nextindex]                                     = $repository->extractEntity($entity);
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                    'combat.enemy.spawn',
                    [
                        'creatureName' => $badguy['creaturename'],
                        'summonName'   => $entity->getCreaturename(),
                    ],
                ];
            }
        }
        elseif (\is_array($creature))
        {
            $newenemies[$nextindex] = $creature;
        }

        \ksort($newenemies);
    }

    /**
     * Allows creatures to heal themselves or another badguy.
     *
     * @param int   $amount Amount of helath to be restored
     * @param mixed $target if false badguy will heal itself otherwise the enemy with this index
     */
    public function battleHeal($amount, $target = false)
    {
        global $newenemies, $enemies, $badguy, $countround, $lotgdBattleContent;

        if ($amount > 0)
        {
            if (false === $target)
            {
                $badguy['creaturehealth'] += $amount;
                $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                    'combat.enemy.heal.self',
                    [
                        'creatureName' => $badguy['creaturename'],
                        'damage'       => $amount,
                    ],
                ];
            }
            else
            {
                if (isset($newenemies[$target]))
                {
                    // Target had its turn already...
                    if (! $newenemies[$target]['dead'])
                    {
                        $newenemies[$target]['creaturehealth'] += $amount;
                        $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                            'combat.enemy.heal.other',
                            [
                                'cretureName' => $badguy['creaturename'],
                                'target'      => $newenemies[$target]['creaturename'],
                                'damage'      => $amount,
                            ],
                        ];
                    }
                }
                elseif (! $enemies[$target]['dead'])
                {
                    $enemies[$target]['creaturehealth'] += $amount;
                    $lotgdBattleContent['battlerounds'][$countround]['enemy'][] = [
                        'combat.enemy.heal.other',
                        [
                            'creatureName' => $badguy['creaturename'],
                            'target'       => $enemies[$target]['creaturename'],
                            'damage'       => $amount,
                        ],
                    ];
                }
            }
        }
    }
}
