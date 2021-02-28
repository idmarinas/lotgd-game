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

namespace Lotgd\Bundle\CoreBundle\Installer\Command;

use Lotgd\Bundle\CoreBundle\Installer\InstallerAbstract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for upgrade to clean install.
 */
abstract class AbstractCommand extends Command
{
    use LockableTrait;

    protected $installer;
    protected $translator;
    protected $bar;

    public function setProgressBar(ProgressBar $bar)
    {
        $this->bar = $bar;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        /* @internal Not need show in list */
        $this->setHidden(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Lotgd\Bundle\CoreBundle\Kernel */
        $app    = $this->getApplication();
        $kernel = $app->getKernel();

        $this->installer->configureConsole($input, $output);
        $this->installer->setProjectDir($kernel->getProjectDir());

        $this->installer->start(); //-- Start install

        //-- Make a database migration
        if ($this->migrateDataBase($this->installer->getMigration()))
        {
            $msg = $this->translator->trans('installer.installation.abort.database', [], InstallerAbstract::TRANSLATOR_DOMAIN);
            $output->writeln("<error>{$msg}</>");

            return Command::FAILURE;
        }

        $step   = 0;
        $steps  = $this->installer->getSteps();
        $result = true;

        //-- Proccess all steps of installer
        do
        {
            $text = $this->translator->trans('installer.progressbar.install.progress.steps', [
                'step'  => $step,
                'total' => $steps,
            ], InstallerAbstract::TRANSLATOR_DOMAIN);
            $this->bar->setMessage($text, 'steps');
            $this->bar->display();

            if ($this->installer->stepStatus($step))
            {
                ++$step;

                continue;
            }

            $result = $this->installer->{"step{$step}"}();

            $this->installer->{$result ? 'setStepComplete' : 'setStepIncomplete'}($step);

            ++$step;
        } while ($result && \method_exists($this->installer, "step{$step}"));

        $this->installer->finish(); //-- Finish install

        return Command::SUCCESS;
    }

    protected function migrateDataBase(int $migration)
    {
        if ( ! $migration)
        {
            return Command::SUCCESS;
        }

        $message = $this->translator->trans('installer.progressbar.install.progress.database', [], InstallerAbstract::TRANSLATOR_DOMAIN);
        $this->bar->setMessage($message, 'steps');
        $this->bar->display();

        $input = new ArrayInput([
            'version' => 'Lotgd\Bundle\CoreBundle\Migration\Version'.$migration,
            '--no-interaction' => true
        ]);
        $input->setInteractive(false); //-- Do not ask any interactive question

        $command = $this->getApplication()->find('doctrine:migrations:migrate');

        return (int) $command->run($input, new NullOutput());
    }
}