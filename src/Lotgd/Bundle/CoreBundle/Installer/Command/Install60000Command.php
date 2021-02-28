<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Installer\Command;

use Lotgd\Bundle\CoreBundle\Installer\Upgrade\Version60000;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for upgrade to 6.0.0 version.
 */
final class Install60000Command extends AbstractCommand
{
    protected static $defaultName = 'lotgd:install:v:60000';

    public function __construct(Version60000 $install, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
    }
}
