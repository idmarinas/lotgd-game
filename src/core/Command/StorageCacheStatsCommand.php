<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.6.0
 */

namespace Lotgd\Core\Command;

use Lotgd\Core\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

/**
 * Show stats of cache directory.
 */
class StorageCacheStatsCommand extends Command
{
    use FormatTrait;

    protected static $defaultName = 'storage:cache_stats';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Show stats of cache')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command to show stats of cache directory.
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Kernel $sm */
        $kernel = $this->getApplication()->getKernel();
        $style  = new SymfonyStyle($input, $output);

        $findFiles = (new Finder())->ignoreUnreadableDirs()->in($kernel->getCacheDir())->files()->notName('.gitkeep');
        $findDir   = (new Finder())->ignoreUnreadableDirs()->in($kernel->getCacheDir())->directories();

        $row = [
            ['Total files', $findFiles->count()],
            ['Total dirs', $findDir->count()],
            ['Total size', self::formatFileSize($kernel->getCacheDir())],
        ];

        $style->table(['Cache stats'], $row);

        return Command::SUCCESS;
    }
}
