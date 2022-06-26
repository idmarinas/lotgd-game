<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.0.0
 */

namespace Lotgd\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
    protected $style;
    protected $doctrine;

    public function __construct(Install $install, TranslatorInterface $translator, EntityManagerInterface $doctrine)
    {
        parent::__construct();

        $this->installer  = $install;
        $this->translator = $translator;
        $this->doctrine   = $doctrine;
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

            return 0;
        }

        $output->writeln('');
        $output->writeln('<fg=green>'.$this->banner().'</>');
        $output->writeln('');

        $text = $this->translator->trans('installer.license.read', [], InstallerAbstract::TRANSLATOR_DOMAIN);
        $output->writeln("<info>{$text}</>");
        $output->writeln('<href=http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode>http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode</>');

        $helper   = $this->getHelper('question');
        $question = new ConfirmationQuestion($this->translator->trans('installer.license.question', [], InstallerAbstract::TRANSLATOR_DOMAIN), false);

        $output->writeln('');
        if ( ! $helper->ask($input, $output, $question))
        {
            $text = $this->translator->trans('installer.license.reject', [], InstallerAbstract::TRANSLATOR_DOMAIN);
            $output->writeln("<error>{$text}</>");

            return 0;
        }

        $text = $this->translator->trans('installer.license.confirmation', [], InstallerAbstract::TRANSLATOR_DOMAIN);
        $output->writeln('');
        $output->writeln("<comment>{$text}</>");

        unset($text);

        return (int) $this->doExecute($input, $output);
    }

    private function doExecute(InputInterface $input, OutputInterface $output)
    {
        /** @var Lotgd\Core\Kernel $app */
        $app         = $this->getApplication();
        $kernel      = $app->getKernel();
        $this->style = new SymfonyStyle($input, $output);
        $this->style->newLine();

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
        $question    = new ConfirmationQuestion(
            $this->translator->trans("installer.check.installation.verify.{$type}", ['version' => $nameVersion], InstallerAbstract::TRANSLATOR_DOMAIN),
            false
        );

        //-- If is incorrect ask to select installed version
        if ( ! $helper->ask($input, $output, $question))
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
            return 0;
        }

        //-- Checks versions need install
        $versionsToInstall      = $this->installer->getVersionsToInstall($fromVersion, $toVersion);
        $countVersionsToInstall = \count($versionsToInstall);

        //-- Information count of versions need upgrade.
        $this->style->note($this->translator->trans('installer.installation.info.total', ['n' => $countVersionsToInstall], InstallerAbstract::TRANSLATOR_DOMAIN));

        //-- Create a progress bar
        $installerBar = $this->getProgressBar($output);
        $installerBar->setMessage($this->translator->trans('installer.progressbar.install.label', [], InstallerAbstract::TRANSLATOR_DOMAIN));

        //-- Start bar
        $installerBar->start($countVersionsToInstall + 1); //-- For clear cache

        //-- Process instalation scripts
        if ($this->processInstallation($input, $output, $installerBar, $versionsToInstall))
        {
            $this->style->error($this->translator->trans('installer.installation.abort.install', [], InstallerAbstract::TRANSLATOR_DOMAIN));

            return 1;
        }

        $this->installer->finish(); //-- Finish installer

        //-- Cache clear
        $this->processCacheClear($installerBar);

        $installerBar->setMessage($this->translator->trans('installer.progressbar.install.end', [], InstallerAbstract::TRANSLATOR_DOMAIN));
        $installerBar->setMessage($this->translator->trans('installer.progressbar.install.progress.completed', [], InstallerAbstract::TRANSLATOR_DOMAIN), 'steps');

        $installerBar->finish(); //-- Ensures that the progress bar is at 100%

        $this->style->newLine();
        $this->style->newLine();
        $this->style->newLine();

        //-- Create user admin (If not have admin)
        $this->createUserAdmin($input, $output);

        return 0;
    }

    private function processInstallation(InputInterface $input, OutputInterface $output, ProgressBar $installerBar, array $versionsToInstall)
    {
        $message = $this->translator->trans('installer.progressbar.install.progress.database', [], InstallerAbstract::TRANSLATOR_DOMAIN);
        $installerBar->setMessage($message, 'steps');
        $installerBar->display();

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

            /** @var Lotgd\Core\Installer\Command\AbstractCommand  $command*/
            $command = $this->getApplication()->find("lotgd:install:v:{$v_id}");
            $command->setProgressBar($installerBar);
            $returnCode = (int) $command->run($input, $output);

            //-- If command fail, abort installation
            if ($returnCode !== 0)
            {
                $this->style->warning($this->translator->trans('installer.installation.abort.command', [
                    'version' => $this->installer->getNameVersion($v_id),
                ], InstallerAbstract::TRANSLATOR_DOMAIN));

                return 1;
            }

            $installerBar->advance(1);
        }

        return 0;
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

    private function createUserAdmin(InputInterface $input, OutputInterface $output)
    {
        /** @var Lotgd\Core\Repository\UserRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:User');
        $superusers = (bool) $repository->getSuperuserCountWithPermit(SU_MEGAUSER);

        //-- Not run if server have 1 admin
        if ($superusers)
        { //-- If exist one super user not allow create more
            return false;
        }

        $this->style->title($this->translator->trans('installer.installation.user.create', [], InstallerAbstract::TRANSLATOR_DOMAIN));
        $this->style->text($this->translator->trans('installer.installation.user.info', [], InstallerAbstract::TRANSLATOR_DOMAIN));

        $command = $this->getApplication()->find('lotgd:user:create');
        $command->run($input, $output);
    }

    private function getProgressBar($output)
    {
        ProgressBar::setPlaceholderFormatterDefinition('memory', function ()
        {
            $mem = \memory_get_usage();
            $colors = $mem > 24_000_000 ? '41;37' : '44;37';

            return "\033[".$colors.'m '.Helper::formatMemory($mem)." \033[0m";
        });

        //-- Progress bar.
        $installerBar = new ProgressBar($output);
        $installerBar->setFormat(" <fg=white;bg=green> %message:-37s% </>\n %current%/%max% %bar% %percent:3s%%\n %steps:39s% \n %remaining:-10s% %memory:39s%");
        $installerBar->setBarCharacter("\033[32m+\033[0m");
        $installerBar->setEmptyBarCharacter("\033[31m-\033[0m");
        $installerBar->setProgressCharacter("\033[32m-\033[0m");

        return $installerBar;
    }

    /**
     * Beautiful banner ^_^.
     */
    private function banner(): string
    {
        return '
██╗      ██████╗ ████████╗ ██████╗ ██████╗      ██████╗ ██████╗ ██████╗ ███████╗
██║     ██╔═══██╗╚══██╔══╝██╔════╝ ██╔══██╗    ██╔════╝██╔═══██╗██╔══██╗██╔════╝
██║     ██║   ██║   ██║   ██║  ███╗██║  ██║    ██║     ██║   ██║██████╔╝█████╗
██║     ██║   ██║   ██║   ██║   ██║██║  ██║    ██║     ██║   ██║██╔══██╗██╔══╝
███████╗╚██████╔╝   ██║   ╚██████╔╝██████╔╝    ╚██████╗╚██████╔╝██║  ██║███████╗
╚══════╝ ╚═════╝    ╚═╝    ╚═════╝ ╚═════╝      ╚═════╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝'
        ;
    }
}
