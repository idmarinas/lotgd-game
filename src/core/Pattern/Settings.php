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

@trigger_error(Settings::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
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
            $this->lotgdSettings = $this->getService(LibSettings::class);
        }

        return $this->lotgdSettings;
    }
}
