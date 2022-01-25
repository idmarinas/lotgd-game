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

class CleanVersion extends InstallerAbstract
{
    protected $upgradeVersion = 'clean';
    protected $hasMigration = 20210127183022;

    //-- Insert data of armors.
    public function step0(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/armor.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of companions.
    public function step1(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/companion.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of creatures.
    public function step2(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/creature.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of cronjobs.
    public function step3(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/cronjob.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of masters.
    public function step4(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/master.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of mounts.
    public function step5(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/mount.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of news.
    public function step6(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/news.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of settings.
    public function step7(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/setting.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of titles.
    public function step8(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/title.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of weapons.
    public function step9(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/clean/weapon.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }
}
