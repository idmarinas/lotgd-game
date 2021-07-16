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

namespace Lotgd\Core\Combat;

class Buffer
{
    private $battle;

    public function __construct(Battle $battle)
    {
        $this->battle = $battle;
    }

    public function calculateBuffFields()
    {
        $this->battle->initialize(true);
        return $this->battle->calculateBuffFields();
    }

    public function restoreBuffFields()
    {
        $this->battle->initialize(true);
        return $this->battle->restoreBuffFields();
    }

    public function applyBuff($name, $buff)
    {
        $this->battle->initialize(true);
        return $this->battle->applyBuff($name, $buff);
    }

    public function applyCompanion($name, $companion, $ignorelimit = false)
    {
        $this->battle->initialize(true);
        return $this->battle->applyCompanion($name, $companion, $ignorelimit);
    }

    public function stripBuff($name)
    {
        $this->battle->initialize(true);
        return $this->battle->stripBuff($name);
    }

    public function stripAllBuffs()
    {
        $this->battle->initialize(true);
        return $this->battle->stripAllBuffs();
    }

    public function hasBuff($name): bool
    {
        $this->battle->initialize(true);
        return $this->battle->hasBuff($name);
    }
}
