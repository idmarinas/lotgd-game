<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.1.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Throwable;
use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Component\Filesystem\Filesystem;

class Version50100 extends InstallerAbstract
{
    protected $upgradeVersion = 50100;
    protected $hasMigration = false;

    //-- Delete old files
    public function step0()
    {
        $fs = new Filesystem();

        try
        {
            $fs->remove([
                $this->getProjectDir().'/config/packages/nucleos_user.yaml',
                $this->getProjectDir().'/src/core/Template/Template.php', //-- To avoid Kernel autoload as service
                $this->getProjectDir().'/src/core/Twig/Loader/LotgdFilesystemLoader.php', //-- To avoid Kernel autoload as service
                $this->getProjectDir().'/templates_core/' //-- Are moved to /themes/LotgdTheme/templates/admin
            ]);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Update array fields
    public function step1(): bool
    {
        try
        {
            $this->doctrine->getConnection()->executeQuery(sprintf(
                "UPDATE `accounts` SET `donationconfig` = 'a:0:{}' WHERE `donationconfig` = '%s' OR `donationconfig` = '%s'",
                's:0:"";',
                'N;'
            ));
            $this->doctrine->getConnection()->executeQuery(sprintf(
                "UPDATE `accounts` SET `prefs` = 'a:0:{}' WHERE `prefs` = '%s' OR `prefs` = '%s'",
                's:0:"";',
                'N;'
            ));
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Update cronjobs
    public function step2(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/50100/cronjobs.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }
}
