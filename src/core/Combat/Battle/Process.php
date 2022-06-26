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

trait Process
{
    /**
     * Check if battle round can continue.
     */
    public function battleRoundContinue(): bool
    {
        //-- If player not have health finalize battle
        if ($this->user['hitpoints'] <= 0)
        {
            return false;
        }

        //-- If all enemies are die finalize battle
        $enemies = array_filter($this->enemies, fn($val) => ! (isset($val['dead']) && $val['dead']) && $val['creaturehealth'] > 0);

        return (bool) \count($enemies);
    }

    /**
     * Check if enemies flees if are alone. (Leader die).
     */
    protected function battleEnemyFleesIfAlone(): void
    {
        if ( ! $this->countEnemiesAlive())
        {
            return;
        }

        $leaderEnemyDies = 0;
        $leaderEnemy     = array_filter($this->enemies, fn($enemy) => $enemy['essentialleader'] ?? false);

        //-- Check if leaders are dead
        foreach ($leaderEnemy as &$leader)
        {
            if ( ! $this->isEnemyAlive($leader))
            {
                $leader['istarget'] = false;
                ++$leaderEnemyDies;

                $this->addContextToRoundEnemy([
                    'battle.flee.multi',
                    ['creatureName' => $leader['creaturename']],
                    $this->getTranslationDomain(),
                ]);
            }
        }
        unset($leader);
        $leadersEnemiesDie = ($leaderEnemyDies === \count($leaderEnemy));

        foreach ($this->enemies as &$enemy)
        {
            if ($enemy['dead'] || $enemy['creaturehealth'] <= 0)
            {
                //-- Unset enemy if not want fight without leader
                if (($enemy['fleesifalone'] ?? false) && $leadersEnemiesDie)
                {
                    $this->addContextToRoundEnemy([
                        'battle.flee.one',
                        ['creatureName' => $enemy['creaturename']],
                        $this->getTranslationDomain(),
                    ]);
                    unset($enemy);
                }

                $badguy['istarget'] = false;
            }
        }
        unset($enemy);
    }

    protected function isEnemyAlive(&$badguy): bool
    {
        if ($badguy['creaturehealth'] <= 0 || (isset($badguy['dead']) && $badguy['dead']))
        {
            $badguy['dead']     = true;
            $badguy['istarget'] = false;

            return false;
        }

        return true;
    }

    protected function isCompanionAlive(&$companion): bool
    {
        if ($companion['hitpoints'] <= 0)
        {
            $msg = $this->tools->substitute(
                $companion['dyingtext'] ?? 'combat.companion.die',
                ...$this->getSubstituteParams($this->enemyTargeted)
            );

            $this->addContextToRoundAlly([$msg, ['companion' => $companion['name']], $this->getTranslationDomain()]);

            if ($companion['cannotdie'] ?? false)
            {
                $companion['cannotdie'] = 0;
            }

            return false;
        }

        return true;
    }

    protected function isPlayerAlive(): bool
    {
        return $this->user['hitpoints'] > 0;
    }
}
