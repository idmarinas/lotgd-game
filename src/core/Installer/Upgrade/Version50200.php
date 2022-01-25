<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Throwable;
use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Component\Filesystem\Filesystem;

class Version50200 extends InstallerAbstract
{
    protected $upgradeVersion = 50200;
    protected $hasMigration = false;

    //-- Delete old files
    public function step0()
    {
        $fs = new Filesystem();

        try
        {
            $fs->remove([
                $this->getProjectDir().'/bin/lotgd',
                $this->getProjectDir().'/data/form/core/',
                $this->getProjectDir().'/src/core/Battle/',
                $this->getProjectDir().'/src/core/Component/',
                $this->getProjectDir().'/src/core/Console/',
                $this->getProjectDir().'/src/core/Factory/',
                $this->getProjectDir().'/src/core/Filter/',
                $this->getProjectDir().'/src/core/Db/',
                $this->getProjectDir().'/src/core/Nav/',
                $this->getProjectDir().'/src/core/Patern/',
                $this->getProjectDir().'/src/core/Translator/',
                $this->getProjectDir().'/src/core/Validator/',
                $this->getProjectDir().'/src/core/Template/Base.php',
                $this->getProjectDir().'/src/core/Template/Theme.php',
                $this->getProjectDir().'/src/core/EventManagerAware.php',
                $this->getProjectDir().'/src/core/Event.php',
                $this->getProjectDir().'/src/core/Http.php',
                $this->getProjectDir().'/src/core/Modules.php',
                $this->getProjectDir().'/src/core/ServiceManager.php',
                $this->getProjectDir().'/src/core/Session.php',
                $this->getProjectDir().'/src/core/Fixed/Cache.php',
                $this->getProjectDir().'/src/core/Fixed/Dbwrapper.php',
                $this->getProjectDir().'/src/core/Fixed/EventManager.php',
                $this->getProjectDir().'/src/core/Fixed/Http.php',
                $this->getProjectDir().'/src/core/Fixed/HookManager.php',
                $this->getProjectDir().'/src/core/Fixed/Locator.php',
                $this->getProjectDir().'/src/core/Fixed/SymfonyForm.php',
            ]);
        }
        catch (Throwable $th)
        {
            return false;
        }

        return true;
    }
}
