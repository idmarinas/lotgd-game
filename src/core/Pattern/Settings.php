<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Lib\Settings as LibSettings;

trait Settings
{
    protected $lotgdSettings;

    /**
     * Get Settings instance.
     */
    public function getLotgdSettings(): LibSettings
    {
        if ( ! $this->lotgdSettings instanceof LibSettings)
        {
            $this->lotgdSettings = $this->getContainer(LibSettings::class);
        }

        return $this->lotgdSettings;
    }
}
