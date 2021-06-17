<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Lotgd\Core\Installer\InstallerAbstract;

class Version50400 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 50400;
    protected $hasMigration = false;

    //-- Delete old files
    public function step0()
    {
        return $this->removeFiles([
            $this->getProjectDir().'/lib/clan/', //-- Deleted old files of clan
            $this->getProjectDir().'/lib/inn/',
        ]);
    }
}
