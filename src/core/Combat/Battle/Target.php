<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

trait Target
{
    private $isChangeTarget = false; //-- This avoid attacks when change target

    /**
     * Automatically chooses the first still living enemy as target for attacks.
     */
    protected function autoTarget(): void
    {
        $targeted = false;

        foreach ($this->enemies as $index => &$badguy)
        {
            $badguy['istarget'] = false;

            if ( ! $targeted && ! $badguy['dead'] && ( ! isset($badguy['cannotbetarget']) || ! $badguy['cannotbetarget']))
            {
                $badguy['istarget'] = true;
                $targeted           = true;

                $this->setTarget($index);
            }
        }

        unset($badguy);
    }

    /**
     * Change target enemy.
     *
     * @param int $newTarget
     */
    protected function changeTarget($newTarget): void
    {
        foreach ($this->enemies as $index => &$badguy)
        {
            $badguy['istarget'] = false; //-- This untarget all enemies

            if ($index == $newTarget) //-- Target selected
            {
                if ( ! ($badguy['cannotbetarget'] ?? false))
                {
                    $badguy['istarget'] = true;

                    $this->isChangeTarget = true;

                    $this->setTarget($index);
                }
                elseif ($badguy['cannotbetarget'])
                {
                    $this->addContextToMessages([
                        'battle.untarget',
                        ['creature_name' => $badguy['creaturename']],
                        $this->getTranslationDomain(),
                    ]);

                    $this->autoTarget(); //-- Select one target if selected targed cant be targeted
                }
            }
        }

        unset($badguy);
    }

    /**
     * Count how much enemies are alive.
     */
    protected function countEnemiesAlive(): int
    {
        //-- Count enemies alive
        $enemies = array_filter($this->enemies, function ($val)
        {
            return ! (isset($val['dead']) && $val['dead']) && $val['creaturehealth'] > 0;
        });

        return \count($enemies);
    }
}
