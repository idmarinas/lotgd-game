<?php

//-- To avoid conflicts if you have a code debugger installed such as xDebug
if (0 != ini_get('max_execution_time'))
{
    set_time_limit(666); //-- Temporary increased limit execution time
}

bdump($session['installer']['moduleoperations']);

/**
 * Configure de installer class.
 */
$installer = LotgdLocator::get(\Lotgd\Core\Installer\Install::class);
$actualVersion = (string) getsetting('installer_version', '-1');

//-- Check if can install this version
$installer->versionInstalled($actualVersion);
//-- Check if is an upgrade or clean install
$installer->isUpgradeOn();
if (! $session['installer']['dbinfo']['upgrade'])
{
    $installer->isUpgradeOff();
}

if (false === $installer->canInstall() && $installer->isUpgrade())
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('stage9.unableUpgrade', ['version' => \Lotgd\Core\Application::VERSION], 'page-installer'));
    \LotgdFlashMessages::addWarningMessage($installer->getFailInstallMessage());

    return redirect('home.php');
}

/*
 * Pre-Install: before sync core tables execute this code of install
 */
$params['preInstall'] = $installer->makePreInstall($installer->getIntVersion($actualVersion));

/*
 * Synchronization of core tables, you can found this entities in "src/core/Entity"
 */
$params['synchronizationTables'] = $installer->makeSynchronizationTables();

/**
 * Insert data
 */
$installer->dataInsertedOff();
if ($session['installer']['dataInserted'] ?? false)
{
    $installer->dataInsertedOn();
}
$params['insertData'] = $installer->makeInsertData();
$session['installer']['dataInserted'] = $installer->dataInserted();

/**
 * Installation of modules
 */
$installer->skipModulesOff();
$installer->setModules($session['installer']['moduleoperations'] ?? []);
if ($session['installer']['skipmodules'] ?? false)
{
    $installer->skipModulesOn();
}

$params['installOfModules'] = $installer->makeInstallOfModules();

rawoutput(LotgdTheme::renderLotgdTemplate('core/pages/installer/stage-9.twig', $params));
