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

namespace Lotgd\Core\Installer\Command;

use Lotgd\Core\Installer\Upgrade\Version60100;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for upgrade to 6.1.0 version.
 */
final class Install60100Command extends AbstractCommand
{
    protected static $defaultName = 'lotgd:install:v:60100';

    public function __construct(Version60100 $install, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
    }
}
