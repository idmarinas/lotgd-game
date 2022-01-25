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

use Throwable;
use Lotgd\Core\Service\Cron\ContentCleanService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContentCleanCommand extends Command
{
    use LockableTrait;

    public const TRANSLATION_DOMAIN = 'console_command';

    protected static $defaultName = 'lotgd:cron:content:clean';

    private $contentClean;
    private $translator;

    public function __construct(ContentCleanService $contentClean, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->contentClean = $contentClean;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Clean old content and comments of data base.')
            ->setHelp(
                <<<'EOT'
                    The <info>%command.name%</info> clean old content and comments of data base.
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
            $this->contentClean->execute();

            $style->text('Clean old content and comments of game data base');
        }
        catch (Throwable $th)
        {
            return 1;
        }

        return 0;
    }
}
