<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Installer\Pattern;

trait Version
{
    /**
     * Versions of game.
     *
     * @var array
     */
    protected $lotgd_versions = [
        'Clean Install'                   => -1, //needed just as a placeholder for new installs.
        '0.9'                             => 900,
        '0.9.1'                           => 901,
        '0.9.2'                           => 902,
        '0.9.3'                           => 903,
        '0.9.4'                           => 904,
        '0.9.5'                           => 905,
        '0.9.6'                           => 906,
        '0.9.7'                           => 907,
        '0.9.8-prerelease.1'              => 908,
        '0.9.8-prerelease.2'              => 909,
        '0.9.8-prerelease.3'              => 910,
        '0.9.8-prerelease.4'              => 911,
        '0.9.8-prerelease.5'              => 912,
        '0.9.8-prerelease.6'              => 913,
        '0.9.8-prerelease.7'              => 914,
        '0.9.8-prerelease.8'              => 915,
        '0.9.8-prerelease.9'              => 916,
        '0.9.8-prerelease.10'             => 917,
        '0.9.8-prerelease.11'             => 918,
        '0.9.8-prerelease.12'             => 919,
        '0.9.8-prerelease.13'             => 920,
        '0.9.8-prerelease.14'             => 921,
        '0.9.8-prerelease.14a'            => 922,
        '1.0.0'                           => 10000,
        '1.0.1'                           => 10001,
        '1.0.2'                           => 10002,
        '1.0.3'                           => 10003,
        '1.0.4'                           => 10004,
        '1.0.5'                           => 10005,
        '1.0.6'                           => 10006,
        '1.1.0 Dragonprime Edition'       => 10100,
        '1.1.1 Dragonprime Edition'       => 10101,
        '1.1.2 Dragonprime Edition'       => 10102,
        '1.1.1.0 Dragonprime Edition +nb' => 10103,
        '1.1.1.1 Dragonprime Edition +nb' => 10104,
        '2.0.0 IDMarinas Edition'         => 20000,
        '2.0.1 IDMarinas Edition'         => 20001,
        '2.1.0 IDMarinas Edition'         => 20100,
        '2.2.0 IDMarinas Edition'         => 20200,
        '2.3.0 IDMarinas Edition'         => 20300,
        '2.4.0 IDMarinas Edition'         => 20400,
        '2.5.0 IDMarinas Edition'         => 20500,
        '2.6.0 IDMarinas Edition'         => 20600,
        '2.7.0 IDMarinas Edition'         => 20700,
        '3.0.0 IDMarinas Edition'         => 30000,
        '4.0.0 IDMarinas Edition'         => 40000, //-- New Installer
        '4.1.0 IDMarinas Edition'         => 40100,
        '4.2.0 IDMarinas Edition'         => 40200,
        '4.3.0 IDMarinas Edition'         => 40300,
        '4.4.0 IDMarinas Edition'         => 40400,
        '4.5.0 IDMarinas Edition'         => 40500,
        '4.6.0 IDMarinas Edition'         => 40600,
        '4.7.0 IDMarinas Edition'         => 40700,
        '4.8.0 IDMarinas Edition'         => 40800,
        '4.9.0 IDMarinas Edition'         => 40900,
        '4.10.0 IDMarinas Edition'        => 41000,
        '4.11.0 IDMarinas Edition'        => 41100,
        '4.12.0 IDMarinas Edition'        => 41200,
        '5.0.0 IDMarinas Edition'         => 50000, //-- New Installer
        '5.1.0 IDMarinas Edition'         => 50100,
        '5.1.1 IDMarinas Edition'         => 50101,
        '5.1.2 IDMarinas Edition'         => 50102,
        '5.1.3 IDMarinas Edition'         => 50103,
        '5.1.4 IDMarinas Edition'         => 50104,
        '5.1.4 IDMarinas Edition'         => 50105,
        '5.1.6 IDMarinas Edition'         => 50106,
        '5.2.0 IDMarinas Edition'         => 50200,
        '5.2.1 IDMarinas Edition'         => 50201,
        '5.2.2 IDMarinas Edition'         => 50202,
        '5.2.3 IDMarinas Edition'         => 50203,
        '5.2.4 IDMarinas Edition'         => 50204,
    ];

    /**
     * Get int value for a string version.
     */
    public function getIntVersion(string $version): int
    {
        return $this->lotgd_versions[$version] ?? 0;
    }

    /**
     * Get name for a numeric version.
     */
    public function getNameVersion(int $version): string
    {
        $version = \max(-1, $version) ?: -1;

        return (string) \array_search($version, $this->lotgd_versions);
    }

    /**
     * Check if ID is a valid version.
     */
    public function isValidVersion(int $version): bool
    {
        $version = \max(-1, $version) ?: -1;

        return false !== \array_search($version, $this->lotgd_versions);
    }

    /**
     * Get array of versions.
     */
    public function getInstallerVersions(): array
    {
        //-- Only version up of 4.12.0 IDMarinas Edition is allowed in installer
        return \array_filter($this->lotgd_versions, function ($version)
        {
            return $version > 41200 || -1 == $version;
        });
    }

    /**
     * Get array of versions.
     */
    public function getFullListOfVersion(): array
    {
        return $this->lotgd_versions;
    }
}
