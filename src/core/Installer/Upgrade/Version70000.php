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

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Contracts\Translation\TranslatorInterface;

class Version70000 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 70000;
    protected $hasMigration   = 0;

    public function __construct(EntityManagerInterface $doctrine, TranslatorInterface $translator)
    {
        $this->doctrine   = $doctrine;
        $this->translator = $translator;
    }

    //-- Delete old files
    public function step0(): bool
    {
        return $this->removeFiles([
            $this->getProjectDir().'/src/ajax/pattern/core/',
            $this->getProjectDir().'/src/ajax/core/Mail.php',
            $this->getProjectDir().'/src/ajax/core/Motd.php',
            $this->getProjectDir().'/src/ajax/core/Petition.php',
            $this->getProjectDir().'/src/ajax/core/Source.php',
            $this->getProjectDir().'/src/ajax/core/Timeout.php',
        ]);
    }
}
