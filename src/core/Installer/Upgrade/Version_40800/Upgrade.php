<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Installer\Upgrade\Version_40800;

use Lotgd\Core\Installer\UpgradeAbstract;
use Tracy\Debugger;

class Upgrade extends UpgradeAbstract
{
    const VERSION_NUMBER = 40800;
    const CONFIG_DIR_GLOBAL = 'config/autoload/global';

    /**
     * Step 1: Sanitize same values.
     */
    public function step1(): bool
    {
        try
        {
            $value = (int) getsetting('pvphardlimitamount');

            savesetting('pvphardlimitamount', $value);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
