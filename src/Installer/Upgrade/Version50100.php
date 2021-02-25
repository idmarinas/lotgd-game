<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.1.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Component\Filesystem\Filesystem;

class Version50100 extends InstallerAbstract
{
    protected $upgradeVersion = 50100;
    protected $migration      = 0;

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
                $this->getProjectDir().'/templates_core/', //-- Are moved to /themes/LotgdTheme/templates/admin
            ]);
        }
        catch (\Throwable $th)
        {
            return false;
        }

        return true;
    }
}
