<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.6.0
 */

namespace Lotgd\Core\Command;

use Lotgd\Core\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command to display information about the current application.
 */
final class AboutCommand extends Command
{
    protected static $defaultName = 'lotgd:about';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Displays information about the LoTGD Core Application')
            ->setHelp(
                <<<'EOT'
                    The <info>%command.name%</info> command displays information about the current LoTGD application.
                    EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        /** @var Kernel $kernel */
        $kernel = $this->getApplication()->getKernel();

        $rows = [
            ['<info>LoTGD Core</>'],
            new TableSeparator(),
            ['Version', Kernel::VERSION],
            ['Environment', $kernel->getEnvironment()],

        ];

        $style->table([], $rows);

        return 0;
    }
}
