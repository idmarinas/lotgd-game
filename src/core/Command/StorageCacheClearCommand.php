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
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Clear cache directory.
 */
class StorageCacheClearCommand extends Command
{
    use FormatTrait;

    protected static $defaultName = 'storage:cache_clear';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Clears the cache')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command clears the cache, ignoring the .gitkeep files.
This allows you to keep the default cache structure.
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
        $fs     = new Filesystem();

        $find = (new Finder())->ignoreUnreadableDirs()->in($kernel->getCacheDir())->files()->notName('.gitkeep');

        //-- Only remove proxy cache in dev environment
        if ($kernel->getEnvironment() == 'prod')
        {
            $find->notPath('DoctrineORMModule/Proxy');
        }

        if ($find->count())
        {
            try
            {
                $fs->remove($find);

                //-- Remove empty dirs
                $findD = new Finder();
                $findD->in($kernel->getCacheDir())->directories()
                    ->filter(function (\SplFileInfo $file)
                    {
                        if ($file->isDir())
                        {
                            $finder = new Finder();
                            $finder->in($file->getPathname())->ignoreDotFiles(false);

                            //-- No delete dirs with .gitkeep files or not are empty
                            if ($finder->count())
                            {
                                return false;
                            }

                            return true;
                        }

                        return true;
                    })
                ;

                $fs->remove($findD);
            }
            catch (IOException $e)
            {
                if ($output->isVerbose())
                {
                    $style->warning($e->getMessage());
                }
            }
        }
        else
        {
            $style->warning('Cache directory is empty, nothing to delete.');
        }

        $style->success('Cache was successfully cleared');

        return Command::SUCCESS;
    }
}
