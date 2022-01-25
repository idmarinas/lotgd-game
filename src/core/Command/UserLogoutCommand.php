<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Command;

use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command to logout 1 user or all.
 */
final class UserLogoutCommand extends Command
{
    use LockableTrait;

    public const TRANSLATION_DOMAIN = 'console_command';

    protected static $defaultName = 'lotgd:user:logout';

    private $repository;
    private $cache;
    private $settings;
    private $translator;

    public function __construct(UserRepository $repository, CacheInterface $cache, Settings $settings, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->cache      = $cache;
        $this->settings   = $settings;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Logout all users with inactive sessions')
            ->setHelp(
                <<<'EOT'
                    The <info>%command.name%</info> command to logout all users with inactive sessions.
                    EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //-- Check if command is running/locked
        if ( ! $this->lock())
        {
            $output->writeln($this->translator->trans('command.running', ['name' => $this->getName()], self::TRANSLATION_DOMAIN));

            return 0;
        }

        $io = new SymfonyStyle($input, $output);

        //-- Logout accounts inactive
        $this->repository->logoutInactiveAccounts((int) $this->settings->getSetting('LOGINTIMEOUT', 900));

        $this->cache->delete('char-list-home-page');

        $io->success('All users with inactive session have been disconnected.');

        return 0;
    }
}
