<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @author IDMarinas
 */

namespace Lotgd\Core\Installer\Pattern;

trait Version
{
    /**
     * Versions of game.
     *
     * @var array
     */
    protected $versions = [
        '-1'                              => -1, //needed just as a placeholder for new installs.
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
        '4.0.0 IDMarinas Edition'         => 40000,
        '4.1.0 IDMarinas Edition'         => 40100,
        '4.2.0 IDMarinas Edition'         => 40200,
        '4.3.0 IDMarinas Edition'         => 40300,
        '4.4.0 IDMarinas Edition'         => 40400,
        '4.5.0 IDMarinas Edition'         => 40500,
        '4.6.0 IDMarinas Edition'         => 40600,
        '4.7.0 IDMarinas Edition'         => 40700,
    ];

    /**
     * Get int value for a string version.
     */
    public function getIntVersion(string $version): int
    {
        return $this->versions[$version] ?? 0;
    }

    /**
     * Get the previous version of the given version.
     */
    public function getPreviusVersion(int $version): int
    {
        $vers = array_values($this->versions);

        $actual = array_search($version, $vers);

        if (false === $actual)
        {
            return 0;
        }
        elseif (isset($vers[$actual - 1]))
        {
            return $vers[$actual - 1];
        }

        return 0;
    }

    /**
     * Get the next version of the given version.
     */
    public function getNextVersion(int $version): int
    {
        $vers = array_values($this->versions);

        $actual = array_search($version, $vers);

        if (false === $actual)
        {
            return 0;
        }
        elseif (isset($vers[$actual + 1]))
        {
            return $vers[$actual + 1];
        }

        return 0;
    }

    /**
     * Get name for a numeric version.
     */
    public function getNameVersion(int $version): string
    {
        return array_search($version, $this->versions);
    }

    /**
     * Get array of versions.
     */
    public function getAllVersions(): array
    {
        return $this->versions;
    }
}
