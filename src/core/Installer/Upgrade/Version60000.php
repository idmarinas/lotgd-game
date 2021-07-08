<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Lotgd\Core\Installer\InstallerAbstract;

class Version60000 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 60000;
    protected $hasMigration   = 20210707115250;

    //-- Delete old files
    public function step0()
    {
        return $this->removeFiles([
            $this->getProjectDir().'/lib/battle/',
            $this->getProjectDir().'/lib/addnews.php',
            $this->getProjectDir().'/lib/charcleanup.php',
            $this->getProjectDir().'/lib/checkban.php',
            $this->getProjectDir().'/lib/creaturefunctions.php',
            $this->getProjectDir().'/lib/buffs.php',
            $this->getProjectDir().'/lib/datetime.php',
            $this->getProjectDir().'/lib/deathmessage.php',
            $this->getProjectDir().'/lib/debuglog.php',
            $this->getProjectDir().'/lib/experience.php',
            $this->getProjectDir().'/lib/fightnav.php',
            $this->getProjectDir().'/lib/forestoutcomes.php',
            $this->getProjectDir().'/lib/gamelog.php',
            $this->getProjectDir().'/lib/increment_specialty.php',
            $this->getProjectDir().'/lib/playerfunctions.php',
            $this->getProjectDir().'/lib/saveuser.php',
            $this->getProjectDir().'/lib/settings.php',
            $this->getProjectDir().'/lib/substitute.php',
            $this->getProjectDir().'/lib/taunt.php',
            $this->getProjectDir().'/lib/tempstat.php',
        ]);
    }
}
