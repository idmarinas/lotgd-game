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
    protected $upgradeVersion = [];

    /**
     * Set that the installation is an upgrade.
     *
     * @return $this
     */
    public function isUpgradeOn(): self
    {
        $this->upgrade = true;

        return $this;
    }

    /**
     * Set that the installation not is an upgrade.
     *
     * @return $this
     */
    public function isUpgradeOff(): self
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

    /**
     * Set that version is upgraded.
     *
     * @param int $version
     *
     * @return self
     */
    public function upgradedVersionOn(int $version): self
    {
        $this->upgradeVersion[$version] = true;

        return $this;
    }

    /**
     * Get if version is upgraded.
     *
     * @param int $version
     *
     * @return bool
     */
    public function isUpgradedVersion(int $version): bool
    {
        return $this->upgradeVersion[$version] ?? false;
    }

    /**
     * Get all upgraded versions in actual install.
     *
     * @return array
     */
    public function getUpgradedVersion(): array
    {
        return $this->upgradeVersion;
    }

    /**
     * Set all upgraded version in actual install.
     *
     * @param array $data
     *
     * @return self
     */
    public function setUpgradedVersion(array $data): self
    {
        $this->upgradeVersion = $data;

        return $this;
    }
}
