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

namespace Lotgd\Core\Installer\Command;

use Lotgd\Core\Installer\Upgrade\Version50400;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for upgrade to 5.4.0 version.
 */
final class Install50400Command extends AbstractCommand
{
    protected static $defaultName = 'lotgd:install:v:50400';

    public function __construct(Version50400 $install, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
    }
}
