<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 8.0.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Lotgd\Core\Installer\InstallerAbstract;
use Throwable;

class Version80000 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 80000;
    protected $hasMigration   = 0;

    //-- Delete old files
    public function step0(): bool
    {
        return $this->removeFiles([
            //-- Deprecations
            $this->getProjectDir().'/lib/configuration/module.php',
            $this->getProjectDir().'/lib/modules',
            $this->getProjectDir().'/lib/modules.php',
            $this->getProjectDir().'/lib/showform.php',
            $this->getProjectDir().'/lib/showtabs.php',
            $this->getProjectDir().'/lib/lotgd_mail.php',
            $this->getProjectDir().'/public/modules.php',
            $this->getProjectDir().'/themes/LotgdModern/templates/admin/page/modules',
            $this->getProjectDir().'/themes/LotgdModern/templates/admin/page/configuration/module.html.twig',
            $this->getProjectDir().'/src/ajax/core',
            $this->getProjectDir().'/src/ajax/core/Entity/ModuleEventHooks.php',
            $this->getProjectDir().'/src/ajax/core/Entity/ModuleHooks.php',
            $this->getProjectDir().'/src/ajax/core/Entity/ModuleObjprefs.php',
            $this->getProjectDir().'/src/ajax/core/Entity/ModuleSettings.php',
            $this->getProjectDir().'/src/ajax/core/Entity/ModuleUserprefs.php',
            $this->getProjectDir().'/src/ajax/core/Entity/Modules.php',
        ]);
    }

    //-- Update cronjobs
    public function step1(): bool
    {
        try
        {
            $sql = file_get_contents(__DIR__.'/data/80000/cron_jobs.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }
}
