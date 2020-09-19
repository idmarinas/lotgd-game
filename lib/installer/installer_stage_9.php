<?php

//-- To avoid conflicts if you have a code debugger installed such as xDebug
if (0 != \ini_get('max_execution_time'))
{
    $time = \ini_get('max_execution_time') * 10;
    \set_time_limit(\max($time, 666)); //-- Temporary increased limit execution time
}

/**
 * Configure de installer class.
 */
$installer     = LotgdLocator::get(\Lotgd\Core\Installer\Install::class);
$actualVersion = (string) getsetting('installer_version', '-1');

//-- Check if can install this version
$installer->versionInstalled($actualVersion);
//-- Check if is an upgrade or clean install
$installer->isUpgradeOn();

if ( ! $session['installer']['dbinfo']['upgrade'])
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
 * Make a upgrade install, avoid in clean install.
 */
$installer->setUpgradedVersion($session['installer']['upgradedVersion'] ?? []);
$params['upgradeInstall']                = $installer->makeUpgradeInstall($installer->getIntVersion($actualVersion));
$session['installer']['upgradedVersion'] = $installer->getUpgradedVersion();

/*
 * Synchronization of core tables, you can found this entities in "src/core/Entity"
 */
$params['synchronizationTables'] = $installer->makeSynchronizationTables();

/*
 * Insert data
 */
$installer->dataInsertedOff();

if ($session['installer']['dataInserted'] ?? false)
{
    $installer->dataInsertedOn();
}
$params['insertData']                 = $installer->makeInsertData();
$session['installer']['dataInserted'] = $installer->dataInserted();

/*
 * Installation of modules
 */
$installer->skipModulesOff();
$installer->setModules($session['installer']['moduleoperations'] ?? []);

if ($session['installer']['skipmodules'] ?? false)
{
    $installer->skipModulesOn();
}

$params['installOfModules'] = $installer->makeInstallOfModules();

rawoutput(LotgdTheme::renderLotgdTemplate('core/page/installer/stage-9.twig', $params));
