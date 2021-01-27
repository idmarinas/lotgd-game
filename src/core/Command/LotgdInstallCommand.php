<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Command;

use Lotgd\Core\Installer\Install;
use Lotgd\Core\Installer\InstallerAbstract;
use Lotgd\Core\Installer\Pattern\FormaterTrait;
use Lotgd\Core\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Command for Install/Upgrade LoTGD.
 */
final class LotgdInstallCommand extends Command
{
    use LockableTrait;
    use FormaterTrait;

    protected static $defaultName = 'lotgd:install';

    protected $installer;
    protected $translator;

    public function __construct(Install $install, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Install/Upgrade LoTGD to lastest version.')
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
            $output->writeln($this->translator->trans('installer.command.running', ['name' => $this->getName()], InstallerAbstract::TRANSLATOR_DOMAIN));

            return Command::SUCCESS;
        }

        return $this->doExecute($input, $output);
    }

    private function doExecute(InputInterface $input, OutputInterface $output)
    {
        /** @var Lotgd\Core\Kernel */
        $app    = $this->getApplication();
        $kernel = $app->getKernel();
        $style  = new SymfonyStyle($input, $output);

        //-- Configure installer
        $this->installer->configureConsole($input, $output);
        $this->installer->setProjectDir($kernel->getProjectDir());
        $this->installer->start(); //-- Start installer

        //-- Determine versions from and to version to install
        $fromVersion = $this->installer->getVersionInstalled();
        $toVersion   = Kernel::VERSION_ID;

        //-- Ask if version detected is correct
        $helper      = $this->getHelper('question');
        $nameVersion = $this->installer->getNameVersion($fromVersion);
        $type        = 'Clean Install' == $nameVersion ? 'clean' : 'version';
        $question    = new ChoiceQuestion(
            $this->translator->trans("installer.check.installation.verify.{$type}", ['version' => $nameVersion], InstallerAbstract::TRANSLATOR_DOMAIN),
            ['No', 'Yes'],
            0
        );

        //-- If is incorrect ask to select installed version
        if ('No' == $helper->ask($input, $output, $question))
        {
            $versions = $this->installer->getInstallerVersions();

            $question = new ChoiceQuestion(
                $this->translator->trans('installer.check.installation.version.choice', [], InstallerAbstract::TRANSLATOR_DOMAIN),
                \array_keys($versions),
                0
            );

            $fromVersion = $this->installer->getIntVersion($helper->ask($input, $output, $question));
        }

        //-- Check if can continue with installation from version
        if ( ! $this->installer->checkInstallation($fromVersion, $toVersion))
        {
            //-- Abort if cant continue with installation
            return Command::SUCCESS;
        }

        //-- Checks versions need install
        $versionsToInstall      = $this->installer->getVersionsToInstall($fromVersion, $toVersion);
        $countVersionsToInstall = \count($versionsToInstall);

        //-- Information count of versions need upgrade.
        $style->note($this->translator->trans('installer.installation.info.total', ['n' => $countVersionsToInstall], InstallerAbstract::TRANSLATOR_DOMAIN));

        //-- Create a progress bar
        $installerBar = $this->getProgressBar($output);
        $installerBar->setMessage($this->translator->trans('installer.progressbar.install.label', [], InstallerAbstract::TRANSLATOR_DOMAIN));

        //-- Start bar
        $installerBar->start($countVersionsToInstall + 2); //-- For migrations DB and clear cache

        //-- Process instalation scripts
        if ($this->processInstallation($input, $output, $installerBar, $style, $versionsToInstall))
        {
            $style->error($this->translator->trans('installer.installation.abort.install', [], InstallerAbstract::TRANSLATOR_DOMAIN));

            return Command::FAILURE;
        }

        $this->installer->finish(); //-- Finish installer

        //-- Cache clear
        $this->processCacheClear($installerBar);

        $installerBar->setMessage($this->translator->trans('installer.progressbar.install.end', [], InstallerAbstract::TRANSLATOR_DOMAIN));
        $installerBar->setMessage($this->translator->trans('installer.progressbar.install.progress.completed', [], InstallerAbstract::TRANSLATOR_DOMAIN), 'steps');

        $installerBar->finish(); //-- Ensures that the progress bar is at 100%

        $style->newLine();

        return Command::SUCCESS;
    }

    private function processInstallation(InputInterface $input, OutputInterface $output, ProgressBar $installerBar, SymfonyStyle $style, array $versionsToInstall)
    {
        $message = $this->translator->trans('installer.progressbar.install.progress.database', [], InstallerAbstract::TRANSLATOR_DOMAIN);
        $installerBar->setMessage($message, 'steps');
        $installerBar->display();

        if ($this->migrateDataBase($installerBar))
        {
            $style->warning($this->translator->trans('installer.installation.abort.database', [], InstallerAbstract::TRANSLATOR_DOMAIN));

            return Command::FAILURE;
        }

        foreach ($versionsToInstall as $v_name => $v_id)
        {
            $message = $this->translator->trans('installer.progressbar.install.progress.version', ['name' => $v_name], InstallerAbstract::TRANSLATOR_DOMAIN);
            $installerBar->setMessage($message);
            $installerBar->setMessage('', 'steps');
            $installerBar->display();

            // -- If command not exist for version go to next
            if ( ! $this->getApplication()->has("lotgd:install:v:{$v_id}"))
            {
                $installerBar->advance(1);

                continue;
            }

            /** @var Lotgd\Core\Installer\Command\AbstractCommand */
            $command = $this->getApplication()->find("lotgd:install:v:{$v_id}");
            $command->setProgressBar($installerBar);
            $returnCode = (int) $command->run($input, $output);

            //-- If command fail, abort installation
            if ($returnCode)
            {
                $style->warning($this->translator->trans('installer.installation.abort.command', [
                    'version' => $this->installer->getNameVersion($v_id),
                ], InstallerAbstract::TRANSLATOR_DOMAIN));

                return Command::FAILURE;
            }

            $installerBar->advance(1);
        }

        return Command::SUCCESS;
    }

    private function migrateDataBase(ProgressBar $installerBar)
    {
        $input = new ArrayInput([
            '--quiet'          => true, //-- Do not output any message
            '--no-interaction' => true,
        ]);
        $input->setInteractive(false); //-- Do not ask any interactive question

        $command = $this->getApplication()->find('doctrine:migrations:migrate');
        $return  = (int) $command->run($input, new NullOutput());

        $installerBar->advance(1);

        return $return;
    }

    private function processCacheClear(ProgressBar $installerBar)
    {
        $input = new ArrayInput([]);
        $input->setInteractive(false); //-- Do not ask any interactive question

        $installerBar->setMessage($this->translator->trans('installer.progressbar.install.label', [], InstallerAbstract::TRANSLATOR_DOMAIN));
        $message = $this->translator->trans('installer.progressbar.install.progress.cache.clear', [], InstallerAbstract::TRANSLATOR_DOMAIN);
        $installerBar->setMessage($message, 'steps');
        $installerBar->display();

        //-- Clear cache
        $command = $this->getApplication()->find('cache:clear');
        $command->run($input, new NullOutput());

        $installerBar->advance(1);
    }

    private function getProgressBar($output)
    {
        ProgressBar::setPlaceholderFormatterDefinition('memory', function ()
        {
            $mem = \memory_get_usage();
            $colors = $mem > 24000000 ? '41;37' : '44;37';

            return "\033[".$colors.'m '.Helper::formatMemory($mem)." \033[0m";
        });

        //-- Progress bar.
        $installerBar = new ProgressBar($output);
        $installerBar->setFormat(" <fg=white;bg=#003800> %message:-37s% </>\n %current%/%max% %bar% %percent:3s%%\n %steps:39s% \n %remaining:-10s% %memory:39s%");
        $installerBar->setBarCharacter("\033[32m+\033[0m");
        $installerBar->setEmptyBarCharacter("\033[31m-\033[0m");
        $installerBar->setProgressCharacter("\033[32m-\033[0m");

        return $installerBar;
    }
}
