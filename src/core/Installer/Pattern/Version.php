<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
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
        '5.1.5 IDMarinas Edition'         => 50105,
        '5.1.6 IDMarinas Edition'         => 50106,
        '5.2.0 IDMarinas Edition'         => 50200,
        '5.2.1 IDMarinas Edition'         => 50201,
        '5.2.2 IDMarinas Edition'         => 50202,
        '5.2.3 IDMarinas Edition'         => 50203,
        '5.2.4 IDMarinas Edition'         => 50204,
        '5.2.5 IDMarinas Edition'         => 50205,
        '5.3.0 IDMarinas Edition'         => 50300,
        '5.3.1 IDMarinas Edition'         => 50301,
        '5.3.2 IDMarinas Edition'         => 50302,
        '5.3.3 IDMarinas Edition'         => 50303,
        '5.3.4 IDMarinas Edition'         => 50304,
        '5.3.5 IDMarinas Edition'         => 50305,
        '5.4.0 IDMarinas Edition'         => 50400,
        '5.4.1 IDMarinas Edition'         => 50401,
        '5.4.2 IDMarinas Edition'         => 50402,
        '5.4.3 IDMarinas Edition'         => 50403,
        '5.5.0 IDMarinas Edition'         => 50500,
        '5.5.1 IDMarinas Edition'         => 50501,
        '5.5.2 IDMarinas Edition'         => 50502,
        '5.5.3 IDMarinas Edition'         => 50503,
        '5.5.4 IDMarinas Edition'         => 50504,
        '5.5.5 IDMarinas Edition'         => 50505,
        '5.5.6 IDMarinas Edition'         => 50506,
        '5.5.7 IDMarinas Edition'         => 50507,
        '5.5.8 IDMarinas Edition'         => 50508,
        '5.5.9 IDMarinas Edition'         => 50509,
        '6.0.0 IDMarinas Edition'         => 60000,
        '6.0.1 IDMarinas Edition'         => 60001,
        '6.0.2 IDMarinas Edition'         => 60002,
        '6.0.3 IDMarinas Edition'         => 60003,
        '6.0.4 IDMarinas Edition'         => 60004,
        '6.1.0 IDMarinas Edition'         => 60100,
        '6.1.1 IDMarinas Edition'         => 60101,
        '6.2.0 IDMarinas Edition'         => 60200,
        '6.2.1 IDMarinas Edition'         => 60201,
        '7.0.0 IDMarinas Edition'         => 70000, //-- Latest version compatible with the old module system (May be)
        '7.0.1 IDMarinas Edition'         => 70001,
        '7.0.2 IDMarinas Edition'         => 70002,
        '7.0.3 IDMarinas Edition'         => 70003,
        '7.0.4 IDMarinas Edition'         => 70004,
        '7.1.0 IDMarinas Edition'         => 70100,
        '7.1.1 IDMarinas Edition'         => 70101,
        '7.1.2 IDMarinas Edition'         => 70102,
        '7.1.3 IDMarinas Edition'         => 70103,
        '7.1.4 IDMarinas Edition'         => 70104,
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

        return in_array($version, $this->lotgd_versions);
    }

    /**
     * Get array of versions.
     */
    public function getInstallerVersions(): array
    {
        //-- Only version up of 4.12.0 IDMarinas Edition is allowed in installer
        return \array_filter($this->lotgd_versions, fn($version) => $version > 41200 || -1 == $version);
    }

    /**
     * Get array of versions.
     */
    public function getFullListOfVersion(): array
    {
        return $this->lotgd_versions;
    }
}
