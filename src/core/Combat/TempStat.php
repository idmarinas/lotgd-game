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

class TempStat
{
    private $battle;

    public function __construct(Battle $battle)
    {
        $this->battle = $battle;
    }

    public function applyTempStat($name, $value, $type = 'add')
    {
        $this->battle->initialize(true);
        return $this->battle->applyTempStat($name, $value, $type);
    }

    public function checkTempStat($name, $color = false)
    {
        $this->battle->initialize(true);
        return $this->battle->checkTempStat($name, $color);
    }

    public function suspendTempStats()
    {
        $this->battle->initialize(true);
        return $this->battle->suspendTempStats();
    }

    public function restoreTempStats()
    {
        $this->battle->initialize(true);
        return $this->battle->restoreTempStats();
    }
}
