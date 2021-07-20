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
