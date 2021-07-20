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

trait Enemy
{
    /**
     * List of enemies.
     *
     * @var array
     */
    private $enemies = [];

    /**
     * Enemy target (value is key in array of enemies).
     *
     * @var int
     */
    private $target = 0;

    /**
     * Data of targed enemy.
     *
     * @var array
     */
    private $enemyTargeted;

    /**
     * Get list of enemies.
     */
    public function getEnemies(): array
    {
        return $this->enemies;
    }

    /**
     * Count enemies targets.
     */
    public function getEnemiesCount(): int
    {
        return \count($this->enemies);
    }

    /**
     * Add one new enemy to list.
     */
    public function addEnemy(array $enemy): self
    {
        $enemy['options'] = $this->getOptions();
        $this->enemies[]  = $enemy;

        return $this;
    }

    /**
     * Get enemy target.
     */
    public function getTarget(): int
    {
        return $this->target;
    }

    /**
     * Set enemy target.
     */
    public function setTarget(int $target): self
    {
        $this->target        = $this->validateTarget($target);
        $this->enemyTargeted = $this->enemies[$this->target];

        return $this;
    }

    /**
     * Set enemies (replace previous).
     */
    protected function setEnemies(array $enemies): self
    {
        foreach ($enemies as &$enemy)
        {
            $enemy['dead']    = ! $this->isEnemyAlive($enemy);
            $enemy['options'] = $this->getOptions();
        }
        unset($enemy);

        $this->enemies = $enemies;

        return $this;
    }

    /**
     * Check if target is a valid enemy.
     */
    protected function validateTarget(int $target): int
    {
        return isset($this->enemies[$target]) ? $target : 0;
    }
}
