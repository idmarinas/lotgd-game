<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Lotgd\Core\Installer\InstallerAbstract;

class Version70000 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 70000;
    protected $hasMigration   = 0;

    //-- Delete old files
    public function step0(): bool
    {
        return $this->removeFiles([
            //-- Jaxon PHP related files
            $this->getProjectDir().'/src/ajax/pattern/core/',
            $this->getProjectDir().'/src/ajax/core/Mail.php',
            $this->getProjectDir().'/src/ajax/core/Motd.php',
            $this->getProjectDir().'/src/ajax/core/Petition.php',
            $this->getProjectDir().'/src/ajax/core/Source.php',
            $this->getProjectDir().'/src/ajax/core/Timeout.php',
            $this->getProjectDir().'/src/Application.php',
            //-- Deprecations
            $this->getProjectDir().'/lib/holiday_texts.php',
            $this->getProjectDir().'/lib/mountname.php',
            $this->getProjectDir().'/lib/mounts.php',
            $this->getProjectDir().'/lib/name.php',
            $this->getProjectDir().'/lib/pageparts.php',
            $this->getProjectDir().'/lib/personal_functions.php',
            $this->getProjectDir().'/lib/pvpsupport.php',
            $this->getProjectDir().'/lib/pvpwarning.php',
            $this->getProjectDir().'/lib/systemmail.php',
            $this->getProjectDir().'/lib/titles.php',
        ]);
    }
}
