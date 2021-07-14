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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class RegenerateAppSecretCommand extends Command
{
    protected static $defaultName = 'lotgd:regenerate:app_secret';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Regenerate APP_SECRET/APP_SECRET_IV for application in .env file')
            ->setHelp(
                <<<'EOT'
                    The <info>%command.name%</info> command regenerate APP_SECRET/APP_SECRET_IV values of .env file for LoTGD application.
                    EOT
            )

            ->addOption('iv', null, InputOption::VALUE_NONE, 'Regenerate value for APP_SECRET_IV')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $file = '.env';

        if (is_file($file) && is_readable($file) && is_writable($file))
        {
            $str         = file_get_contents($file);
            $optionValue = $input->getOption('iv');
            $fs          = new Filesystem();

            $key    = $optionValue ? 'APP_SECRET_IV' : 'APP_SECRET';
            $length = $optionValue ? 8 : 16;

            $pattern = "/^(?<secret>{$key}=.+)$/m";

            preg_match($pattern, $str, $matches);

            if (\is_string($matches['secret']))
            {
                $secret = bin2hex(random_bytes($length));
                $str    = preg_replace("/{$matches['secret']}/", "{$key}={$secret}", $str);

                $fs->dumpFile($file, $str);

                $io->success("New {$key} was generated: {$secret}");
            }
            else
            {
                $io->warning("Not find {$key} in file '{$file}'");
            }

            return 0;
        }

        $io->warning('Not find file ".env" or not is readable or writable.');

        return 0;
    }
}
