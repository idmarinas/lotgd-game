<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class RegenerateAppSecretIvCommand extends Command
{
    protected static $defaultName = 'lotgd:regenerate:app_secret_iv';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Regenerate APP_SECRET_IV for application in .env file')
            ->setHelp(
                <<<'EOT'
                    The <info>%command.name%</info> command regenerate APP_SECRET_IV value of .env file for LoTGD application.
                    EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $file = '.env';

        if (\is_file($file) && \is_readable($file) && \is_writable($file))
        {
            $str     = \file_get_contents($file);
            $pattern = '/^(?<secret>APP_SECRET_IV=.+)$/m';

            \preg_match($pattern, $str, $matches);

            if (\is_string($matches['secret']))
            {
                $fs     = new Filesystem();
                $secret = \bin2hex(\random_bytes(8));
                $str    = \preg_replace("/{$matches['secret']}/", "APP_SECRET_IV={$secret}", $str);

                $fs->dumpFile('.env', $str);

                $io->success("New APP_SECRET_IV was generated: {$secret}");
            }
            else
            {
                $io->warning('Not find APP_SECRET_IV in file ".env"');
            }

            return 0;
        }

        $io->warning('Not find file ".env" or not is readable or writable.');

        return 0;
    }
}
