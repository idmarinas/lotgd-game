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

namespace Lotgd\Core\Installer\Command;

use Lotgd\Core\Installer\Upgrade\Version60200;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for upgrade to 6.2.0 version.
 */
final class Install60200Command extends AbstractCommand
{
    protected static $defaultName = 'lotgd:install:v:60200';

    public function __construct(Version60200 $install, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
    }
}
