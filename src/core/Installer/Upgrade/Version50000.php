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

namespace Lotgd\Core\Installer\Upgrade;

use Throwable;
use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Component\Filesystem\Filesystem;

class Version50000 extends InstallerAbstract
{
    protected $upgradeVersion = 50000;
    protected $hasMigration = false;

    //-- Delete old files
    public function step0()
    {
        $fs = new Filesystem();

        try
        {
            $fs->remove([
                $this->getProjectDir().'/config/lotgd.config.php',
                $this->getProjectDir().'/config/development.config.php',
                $this->getProjectDir().'/config/development.config.php.dist',
                $this->getProjectDir().'/config/development/',
                $this->getProjectDir().'/config/autoload/'
            ]);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Delete table translations
    public function step1(): bool
    {
        try
        {
            $this->doctrine->getConnection()->executeQuery('DROP TABLE IF EXISTS `translations`');
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Delete table untranslated
    public function step2(): bool
    {
        try
        {
            $this->doctrine->getConnection()->executeQuery('DROP TABLE IF EXISTS `untranslated`');
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Delete table nastywords
    public function step3(): bool
    {
        try
        {
            $this->doctrine->getConnection()->executeQuery('DROP TABLE IF EXISTS `nastywords`');
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Delete old cache folder
    public function step4()
    {
        $fs = new Filesystem();

        try
        {
            $fs->remove($this->getProjectDir().'/storage/cache/');
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }
}
