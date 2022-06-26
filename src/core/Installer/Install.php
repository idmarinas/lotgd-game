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

namespace Lotgd\Core\Installer;

use Lotgd\Core\Installer\Pattern\CheckInstallation;
use Lotgd\Core\Installer\Pattern\Version;
use Throwable;
use Lotgd\Core\Entity\Settings as EntitySettings;
use Lotgd\Core\Kernel;
use Symfony\Component\Filesystem\Filesystem;

class Install extends InstallerAbstract
{
    use CheckInstallation;
    use Version;

    protected $installedVersionData;
    protected $installedVersionDataFile;

    /**
     * Determine the installed version.
     */
    public function getVersionInstalled(): int
    {
        $this->installedVersionDataFile = $this->getDirData().'/installed_version_data.data';

        if (\is_file($this->installedVersionDataFile))
        {
            $this->installedVersionData = \file_get_contents($this->installedVersionDataFile);
        }

        if ($this->installedVersionData)
        {
            return $this->installedVersionData;
        }

        //-- Check in version 5.0.0 and up
        try
        {
            $repo = $this->doctrine->getRepository(EntitySettings::class);
            $version = $repo->findOneBy(['setting' => 'installer_version_id']);

            $versionInstalled = (int) $version->getValue();
        }
        catch (Throwable $th)
        {
            //-- No version installed is a new install
            $versionInstalled = 0;
        }

        //-- Check for installations 4.12.0 and down.
        if ( $versionInstalled === 0)
        {
            try
            {
                $repo = $this->doctrine->getRepository(EntitySettings::class);
                $version = $repo->findOneBy(['setting' => 'installer_version']);

                $versionInstalled = $this->getIntVersion($version->getValue());
            }
            catch (Throwable $th)
            {
                //-- No version installed is a new install
                $versionInstalled = 0;
            }
        }

        $versions = $this->getFullListOfVersion();

        $this->installedVersionData = $versions[\array_search($versionInstalled, $versions)] ?? -2;

        return $this->installedVersionData;
    }

    /**
     * Get an array with versions that need install.
     */
    public function getVersionsToInstall(int $fromVersion, int $toVersion): array
    {
        return \array_filter($this->getInstallerVersions(), fn($version) => ($version > $fromVersion && $version <= $toVersion) || -1 == $fromVersion);
    }

    public function start(): bool
    {
        $this->getVersionInstalled();

        return true;
    }

    public function finish(): bool
    {
        //-- Save installation version in file
        (new Filesystem())->dumpFile($this->installedVersionDataFile, Kernel::VERSION_ID);

        //-- Save installation version in Data Base
        try
        {
            $versionId = new EntitySettings();
            $version = new EntitySettings();

            $versionId->setSetting('installer_version_id')
                ->setValue(Kernel::VERSION_ID)
            ;
            $version->setSetting('installer_version')
                ->setValue(Kernel::VERSION)
            ;

            $this->doctrine->persist($versionId);
            $this->doctrine->persist($version);
            $this->doctrine->flush();
        }
        catch (Throwable $th)
        {
            //-- No need capture
        }

        return true;
    }
}
