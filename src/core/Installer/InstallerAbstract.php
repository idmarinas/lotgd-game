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

namespace Lotgd\Core\Installer;

use Throwable;
use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Entity\Settings as EntitySettings;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class InstallerAbstract
{
    public const TRANSLATOR_DOMAIN = 'app_installer';

    protected $upgradeVersion;

    protected $totaSteps;
    protected $style;
    protected $output;
    protected $input;
    protected $doctrine;
    protected $translator;
    protected $stepsProcessed;
    protected $stepsProcessedFile;
    protected $dataDir      = ''; //-- Directory of data "data/"
    protected $hasMigration = false;

    public function __construct(EntityManagerInterface $doctrine, TranslatorInterface $translator)
    {
        $this->doctrine   = $doctrine;
        $this->translator = $translator;
    }

    /**
     * First step of install/upgrade.
     */
    public function start(): bool
    {
        $this->stepsProcessedFile = $this->getDirData()."/upgrades/upgrade_{$this->upgradeVersion}_steps_process.data";
        $this->stepsProcessed     = [];

        if (is_file($this->stepsProcessedFile))
        {
            $this->stepsProcessed = json_decode(file_get_contents($this->stepsProcessedFile), true, 512, JSON_THROW_ON_ERROR);

            return true;
        }

        //-- Check if are in Data Base
        try
        {
            $repo    = $this->doctrine->getRepository(EntitySettings::class);
            $version = $repo->findOneBy(['setting' => "installer_upgrade_v_{$this->upgradeVersion}"]);

            $this->stepsProcessed = json_decode($version->getValue(), true, 512, JSON_THROW_ON_ERROR);
        }
        catch (Throwable $th)
        {
            //-- No need capture
        }

        return true;
    }

    /**
     * Last step of install/upgrade.
     */
    public function finish(): bool
    {
        //-- Save upgrade progress for version in file
        (new Filesystem())->dumpFile($this->stepsProcessedFile, json_encode($this->stepsProcessed, JSON_THROW_ON_ERROR));

        //-- Save upgrade progress version in Data Base
        try
        {
            $version = new EntitySettings();

            $version->setSetting("installer_upgrade_v_{$this->upgradeVersion}")
                ->setValue(json_encode($this->stepsProcessed, JSON_THROW_ON_ERROR))
            ;

            $this->doctrine->persist($version);
            $this->doctrine->flush();
        }
        catch (Throwable $th)
        {
            //-- No need capture
        }

        return true;
    }

    //-- Get totals steps
    public function getSteps(): int
    {
        if ( ! $this->totaSteps)
        {
            $methods = get_class_methods($this);
            //-- Count only step{digits} methods
            $steps = array_filter($methods, function ($val)
            {
                return (bool) preg_match('/^step\d+$/im', $val);
            });

            $this->totaSteps = \count($steps);
        }

        //-- Always +1 for count step `start()` and `finish()`
        return $this->totaSteps + 2;
    }

    //-- Set step as complete
    public function setStepComplete($step)
    {
        $this->stepsProcessed["step-{$step}"] = true;
    }

    //-- Set step as incomplete
    public function setStepIncomplete($step)
    {
        $this->stepsProcessed["step-{$step}"] = false;
    }

    //-- Check if step is completed or not
    public function stepStatus($step)
    {
        return $this->stepsProcessed["step-{$step}"] ?? false;
    }

    public function getDirData(): string
    {
        return $this->dataDir.'/data/installer';
    }

    public function setProjectDir(string $dir): self
    {
        $this->dataDir = $dir;

        return $this;
    }

    public function getProjectDir(): string
    {
        return $this->dataDir;
    }

    /**
     * Configure command with input and output.
     */
    public function configureConsole(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->style  = new SymfonyStyle($input, $output);
    }

    public function hasMigration(): int
    {
        return (int) $this->hasMigration;
    }
}
