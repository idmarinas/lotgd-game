<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.1
 */

namespace Lotgd\Core\Installer\Command;

use Lotgd\Core\Installer\Upgrade\Version50201;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for upgrade to 5.2.1 version.
 */
final class Install50201Command extends AbstractCommand
{
    protected static $defaultName = 'lotgd:install:v:50201';

    public function __construct(Version50201 $install, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
    }
}
