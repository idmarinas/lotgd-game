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
     * @param int $amount Amount of health to be restored
     * @param int $target Enemy to heal (index ID of array of enemies)
     * @param int $self   The healer ID (index ID of array of enemies)
     */
    public function battleHeal(int $amount, int $target, int $healer): void
    {
        if ($amount <= 0)
        {
            return;
        }

        if ($target === $healer && isset($this->enemies[$healer]))
        {
            $this->enemies[$healer] += $amount;
            $this->addContextToRoundEnemy([
                'combat.enemy.heal.self',
                [
                    'creatureName' => $this->enemies[$healer]['creaturename'],
                    'damage'       => $amount,
                ],
            ]);
        }
        elseif (isset($this->enemies[$target]) && $this->isEnemyAlive($this->enemies[$target]))
        {
            $this->enemies[$target]['creaturehealth'] += $amount;
            $this->addContextToRoundEnemy([
                'combat.enemy.heal.other',
                [
                    'cretureName' => $this->enemies[$healer]['creaturename'],
                    'target'      => $this->enemies[$target]['creaturename'],
                    'damage'      => $amount,
                ],
            ]);
        }
    }
}
