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
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command to display information about the current application.
 */
final class AboutCommand extends Command
{
    use FormatTrait;

    protected static $defaultName = 'about';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Displays information about the current Application')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command displays information about the current LoTGD application.

The <info>PHP</info> section displays important configuration that could affect your application. The values might
be different between web and CLI.
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

        /** @var Kernel $sm */
        $kernel = $this->getApplication()->getKernel();

        $rows = [
            ['<info>LoTGD Core</>'],
            new TableSeparator(),
            ['Version', Kernel::VERSION],
            ['Environment', $kernel->getEnvironment()],
            ['Cache directory', self::formatPath($kernel->getCacheDir(), $kernel->getProjectDir()).' (<comment>'.self::formatFileSize($kernel->getCacheDir()).'</>)'],
            ['Log directory', self::formatPath($kernel->getLogDir(), $kernel->getProjectDir()).' (<comment>'.self::formatFileSize($kernel->getLogDir()).'</>)'],
            new TableSeparator(),

            ['<info>PHP</>'],
            new TableSeparator(),
            ['Version', \PHP_VERSION],
            ['Architecture', (\PHP_INT_SIZE * 8).' bits'],
            ['Intl locale', \class_exists('Locale', false) && \Locale::getDefault() ? \Locale::getDefault() : 'n/a'],
            ['Timezone', \date_default_timezone_get().' (<comment>'.(new \DateTime())->format(\DateTime::W3C).'</>)'],
            ['OPcache', \extension_loaded('Zend OPcache') && \filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false'],
            ['APCu', \extension_loaded('apcu') && \filter_var(\ini_get('apc.enabled'), \FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false'],
            ['Xdebug', \extension_loaded('xdebug') ? 'true' : 'false'],
        ];

        $style->table([], $rows);

        return Command::SUCCESS;
    }
}
