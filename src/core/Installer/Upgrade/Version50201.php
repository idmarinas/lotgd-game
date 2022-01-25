<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.1
 */

namespace Lotgd\Core\Installer\Upgrade;

use Throwable;
use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Component\Filesystem\Filesystem;

class Version50201 extends InstallerAbstract
{
    protected $upgradeVersion = 50201;
    protected $hasMigration = false;

    //-- Delete old files
    public function step0()
    {
        $fs = new Filesystem();

        try
        {
            $fs->remove([
                $this->getProjectDir().'/src/core/Twig/Extension/AdvertisingGoogle.php',
            ]);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }
}
