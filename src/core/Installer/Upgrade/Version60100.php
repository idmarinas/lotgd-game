<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Contracts\Translation\TranslatorInterface;

class Version60100 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 60100;
    protected $hasMigration   = 20210805104627;

    public function __construct(EntityManagerInterface $doctrine, TranslatorInterface $translator)
    {
        $this->doctrine   = $doctrine;
        $this->translator = $translator;
    }

    //-- Delete old files
    public function step0(): bool
    {
        return $this->removeFiles([
            $this->getProjectDir().'/lib/newday/db_recalc.php',
            $this->getProjectDir().'/lib/newday/dragonpointspend.php',
            $this->getProjectDir().'/lib/newday/newday.php',
            $this->getProjectDir().'/lib/newday/setrace.php',
            $this->getProjectDir().'/lib/newday/setspecialty.php',
        ]);
    }
}