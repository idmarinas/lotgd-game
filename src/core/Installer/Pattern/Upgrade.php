<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Installer\Pattern;

trait Upgrade
{
    protected $upgrade = false;

    /**
     * Set that the installation is an upgrade.
     *
     * @return $this
     */
    public function isUpgradeOn()
    {
        $this->upgrade = true;

        return $this;
    }

    /**
     * Set that the installation not is an upgrade.
     *
     * @return $this
     */
    public function isUpgradeOff()
    {
        $this->upgrade = false;

        return $this;
    }

    /**
     * Get if the installation is a upgrade.
     *
     * @return bool
     */
    public function isUpgrade(): bool
    {
        return $this->upgrade;
    }
}
