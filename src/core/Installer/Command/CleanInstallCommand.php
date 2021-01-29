<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Installer\Command;

use Lotgd\Core\Installer\Upgrade\CleanVersion;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for upgrade a clean install.
 */
final class CleanInstallCommand extends AbstractCommand
{
    protected static $defaultName = 'lotgd:install:v:-1';

    public function __construct(CleanVersion $install, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
    }
}
