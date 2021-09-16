<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.2.0
 */

namespace Lotgd\Core\Installer\Upgrade;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Installer\InstallerAbstract;
use Symfony\Contracts\Translation\TranslatorInterface;

class Version60200 extends InstallerAbstract
{
    use DeleteFilesTrait;

    protected $upgradeVersion = 60200;
    protected $hasMigration   = 20210908113907;

    public function __construct(EntityManagerInterface $doctrine, TranslatorInterface $translator)
    {
        $this->doctrine   = $doctrine;
        $this->translator = $translator;
    }

    //-- Delete old files
    public function step0(): bool
    {
        return $this->removeFiles([
            $this->getProjectDir().'/lib/graveyard/',
        ]);
    }
}
