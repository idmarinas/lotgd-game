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
     * Adds a new creature to the badguy array.
     *
     * @param array     $badguy   creature that spawn the new creature
     * @param int|array $creature A standard badguy array. If numeric, the corresponding badguy will be loaded from the database.
     */
    public function battleSpawn(array $badguy, $creature = null): void
    {
        if (is_numeric($creature))
        {
            $repository = $this->doctrine->getRepository('LotgdCore:Creatures');
            $entity     = $repository->find($creature);

            if ($entity)
            {
                $this->addEnemy($repository->extractEntity($entity));
                $this->addContextToRoundEnemy([
                    'combat.enemy.spawn',
                    [
                        'creatureName' => $badguy['creaturename'],
                        'summonName'   => $entity->getCreaturename(),
                    ],
                ]);
            }
        }
        elseif (\is_array($creature))
        {
            $this->addEnemy($creature);
            $this->addContextToRoundEnemy([
                'combat.enemy.spawn',
                [
                    'creatureName' => $badguy['creaturename'],
                    'summonName'   => $creature['creaturename'],
                ],
            ]);
        }
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
                    if ( ! $newenemies[$target]['dead'])
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
                elseif ( ! $enemies[$target]['dead'])
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
