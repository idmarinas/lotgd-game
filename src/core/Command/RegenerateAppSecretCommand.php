<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
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

class RegenerateAppSecretCommand extends Command
{
    protected static $defaultName = 'lotgd:regenerate:app_secret';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $file = '.env';

        if (\is_file($file) && \is_readable($file) && \is_writable($file))
        {
            $str     = \file_get_contents($file);
            $pattern = '/^(?<secret>APP_SECRET=.+)$/m';

            \preg_match($pattern, $str, $matches);

            if (\is_string($matches['secret']))
            {
                $fs     = new Filesystem();
                $secret = \bin2hex(\random_bytes(16));
                $str    = \preg_replace("/{$matches['secret']}/", "APP_SECRET={$secret}", $str);

                $fs->dumpFile('.env', $str);

                $io->success("New APP_SECRET was generated: {$secret}");
            }
            else
            {
                $io->warning('Not find APP_SECRET in file ".env"');
            }

            return Command::SUCCESS;
        }

        $io->warning('Not find file ".env" or not is readable or writable.');

        return Command::SUCCESS;
    }
}
