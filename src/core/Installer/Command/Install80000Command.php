<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 8.0.0
 */

namespace Lotgd\Core\Installer\Command;

use Lotgd\Core\Installer\Upgrade\Version80000;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // -- Add new clean version to executed list
        $params = new ArrayInput([
            'version' => 'DoctrineMigrations\Version20220903124947',
            '--add'   => true,
        ]);
        $params->setInteractive(false);

        $command = $this->getApplication()->find('doctrine:migrations:version');

        try
        {
            $command->run($params, new NullOutput());
        }
        catch (\Throwable $th)
        {
            //-- Not break execute if migration are in DB
        }

        // -- Execute command
        return parent::execute($input, $output);
    }
}
