<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Installer\Pattern;

trait CanInstall
{
    protected $versionInstalled = 0;
    protected $cantInstallMessage;

    /**
     * Check if can install this version of game.
     *
     * @return bool
     */
    public function canInstall(): bool
    {
        $version = $this->getInstalledVersion();

        //-- Can't install it: is the same version
        if (\Lotgd\Core\Application::VERSION_NUMBER == $version)
        {
            $this->cantInstallMessage = \LotgdTranslator::t('canInstall.version.same', [], 'app-installer');

            return false;
        }
        //-- This new system can only upgrade from version 3.0.0 IDMarinas Edition
        elseif ($version < 30000)
        {
            $this->cantInstallMessage = \LotgdTranslator::t('canInstall.version.less', [], 'app-installer');

            return false;
        }

        return true;
    }

    /**
     * Set currently installed version.
     *
     * @param string $version
     *
     * @return $this
     */
    public function versionInstalled(string $version)
    {
        $this->versionInstalled = $version;

        return $this;
    }

    /**
     * Get the installation failure message.
     *
     * @return array|string
     */
    public function getFailInstallMessage()
    {
        return $this->cantInstallMessage;
    }

    /**
     * Get int value of installed version.
     *
     * @return int
     */
    private function getInstalledVersion(): int
    {
        $version = $this->getIntVersion($this->versionInstalled);

        if (0 === $version)
        {
            return (int) $this->versionInstalled;
        }

        return $version;
    }
}
