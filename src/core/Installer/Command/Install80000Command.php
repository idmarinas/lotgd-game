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

namespace Lotgd\Core\Installer\Command;

use Lotgd\Core\Installer\Upgrade\Version80000;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for upgrade to 8.0.0 version.
 */
final class Install80000Command extends AbstractCommand
{
    protected static $defaultName = 'lotgd:install:v:80000';

    public function __construct(Version80000 $install, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
    }
}
