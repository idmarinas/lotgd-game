<?php

//-- To avoid conflicts if you have a code debugger installed such as xDebug
if (0 != ini_get('max_execution_time'))
{
    set_time_limit(660); //-- Temporary increased limit execution time
}

/**
 * Configure de installer class.
 */
$installer = LotgdLocator::get(\Lotgd\Core\Installer\Install::class);

//-- Check if can install this version
$installer->versionInstalled((string) getsetting('installer_version', '-1'));
//-- Check if is an upgrade or clean install
$installer->isUpgradeOn();
if (false == $session['dbinfo']['upgrade'])
{
    $installer->isUpgradeOff();
}

if (false === $installer->canInstall() && $installer->isUpgrade())
{
    page_header('Unable to upgrade');
    addnav('Home', 'home.php');

    output('`c`b`4Unable to upgrade to version %s`0´b´c`n', \Lotgd\Core\Application::VERSION);
    output($installer->getFailInstallMessage());

    page_footer();
}

output('`c`b`@Installing/Updating the game`0´b´c');

/*
 * Pre-Install: before update core tables execute this code of install
 */
output('`b`@Performing a pre-installation`0´b`n');
output('`2The intention of this is to be able to migrate the data to the new structure of the tables, for each new version of the core.`n');
output('This is necessary to pass data to new tables, useful when modifying tables by deleting fields and renaming them.`n');
output('This tries to guarantee the integrity of the data and that they are not lost in the updates.`0`n');

output('`n`@Pre-install Logs:`0`n');
rawoutput('<div class="ui segment">');
$pre = $installer->makePreInstall();
foreach($pre as $value)
{
    output($value);
}
rawoutput('</div>');

output('`@`bBuilding the Tables´b`0`n');
output("`2I'm now going to build the tables.");
output('If this is an upgrade, your current tables will be brought in line with the current version.');
output("If it's an install, the necessary tables will be placed in your database.`0`n");

/*
 * Synchronization of core tables, you can found this entities in "src/core/Entity"
 */
output('`n`@Table Synchronization Logs:`0`n');
output("`2It's need to keep the tables synchronized to ensure data integrity.`0`n");
rawoutput('<div class="ui segment">');
$sync = $installer->makeSynchronizationTables();
foreach($sync as $value)
{
    output($value);
}
rawoutput('</div>');

/**
 * Insert data
 */
output('`n`@Insert data Logs:`0`n');
output("`2The tables now have new fields and columns added, I'm going to begin importing data now.`0`n");
rawoutput('<div class="ui segment">');
$installer->dataInsertedOff();
if ($session['installer']['dataInserted'] ?? false)
{
    $installer->dataInsertedOn();
}
$data = $installer->makeInsertData();
$session['installer']['dataInserted'] = $installer->dataInserted();
foreach($data as $value)
{
    output($value);
}
rawoutput('</div>');

/**
 * Installation of modules
 */
$installer->skipModulesOff();
$installer->setModules($session['moduleoperations'] ?? []);
if ($session['skipmodules'] ?? false)
{
    $installer->skipModulesOn();
}

output('`n`@Modules Logs:`0`n');
output("`2Now I'll install and configure your modules.`0`n");
rawoutput('<div class="ui segment">');
$data = $installer->makeInstallOfModules();
foreach($data as $value)
{
    output($value);
}
rawoutput('</div>');

output("`n`n`^You're ready for the next step.");
