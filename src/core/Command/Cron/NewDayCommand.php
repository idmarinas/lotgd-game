<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.2.0
 */

namespace Lotgd\Core\Command\Cron;

use Lotgd\Core\Event\Core;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class NewDayCommand extends Command
{
    use LockableTrait;

    public const TRANSLATION_DOMAIN = 'console_command';

    protected static $defaultName = 'lotgd:cron:game:newday';

    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        parent::__construct();

        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Generate a new game day. (Not triger old module system hook)')
            ->setHelp(
                <<<'EOT'
                    The <info>%command.name%</info> generate a new game day. (Not triger old module system hook)
                    EOT
            )
            ->setHidden(true) //-- This command is intended for use for cronjobs
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //-- Check if command is running/locked
        if ( ! $this->lock())
        {
            $output->writeln($this->translator->trans('command.running', ['name' => $this->getName()], self::TRANSLATION_DOMAIN));

            return 0;
        }

        $style = new SymfonyStyle($input, $output);

        try
        {
            $this->dispatcher->dispatch(new Core(), Core::NEWDAY_RUNONCE);

            $style->text('Generate a new game day');
        }
        catch (\Throwable $th)
        {
            return 1;
        }

        return 0;
    }
}
