<?php

//translator ready
//addnews ready
//mail ready

define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);
define('IS_INSTALLER', true);

/**
 * Checking basic prerequisites for LOTGD.
 */

//php 7.0 or better is required for this version
$requirements_met = true;
$php_met = true;
$memory_met = true;
$execution_met = true;

$memoryLimit = @ini_get('memory_limit');
preg_match("#^(\d+)(\w+)$#", strtolower($memoryLimit), $match);

$memoryLimit = intval($memoryLimit);

if ('g' == $match[2])
{
    $memoryLimit = intval($memoryLimit) * 1024 * 1024 * 1024;
}
elseif ('m' == $match[2])
{
    $memoryLimit = intval($memoryLimit) * 1024 * 1024;
}
elseif ('k' == $match[2])
{
    $memoryLimit = intval($memoryLimit) * 1024;
}

if ($memoryLimit < 128 * 1024 * 1024)
{
    $requirements_met = false;
    $memory_met = false;
}

$executionTime = @ini_get('max_execution_time');
//-- For avoid error when execute xDebug or similar
if ($executionTime < 30 && 0 != $executionTime)
{
    $requirements_met = false;
    $execution_met = false;
}

if (version_compare(PHP_VERSION, '7.0.0') < 0)
{
    $requirements_met = false;
    $php_met = false;
}

if (! $requirements_met)
{
    //we have NO output object possibly :( hence no nice formatting
    echo '<h1>Requirements not sufficient</h1><br><br><big>';

    if (! $php_met)
    {
        echo sprintf('You need PHP 7.0 to install this version. Please upgrade from your existing PHP version %s.<br>', PHP_VERSION);
    }

    if (! $memory_met)
    {
        echo 'Your PHP memory limit is too low. It needs to be set to 128M or more.<br>';
    }

    if (! $execution_met)
    {
        echo 'Your PHP execution time is too low. It needs to be set to 30 or more.<br>';
    }
    echo '</big>';

    exit(1);
}

//-- Need because this check is before include common.php
require_once 'vendor/autoload.php'; //-- Autoload class for new options of game

if (! file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
{
    define('DB_NODB', true);
}

require_once 'common.php';

$noinstallnavs = false;

invalidatedatacache('gamesettings');

tlschema('installer');

$stages = [
    '1. Introduction',
    '2. License Agreement',
    '3. I Agree',
    '4. Database Info',
    '5. Test Database',
    '6. Examine Database',
    '7. Write dbconnect file',
    '8. Install Type',
    '9. Set Up Modules',
    '10. Build Tables',
    '11. Admin Accounts',
    '12. Done!',
];

$recommended_modules = [
    'abigail',
    'breakin',
    'calendar',
    'cedrikspotions',
    'collapse',
    'crazyaudrey',
    'crying',
    'dag',
    'darkhorse',
    'distress',
    'dragonattack',
    'drinks',
    'drunkard',
    'expbar',
    'fairy',
    'findgem',
    'findgold',
    'foilwench',
    'forestturn',
    'game_dice',
    'game_stones',
    'gardenparty',
    'ghosttown',
    'glowingstream',
    'goldmine',
    'grassyfield',
    'haberdasher',
    'healthbar',
    'innchat',
    'kitchen',
    'klutz',
    'lottery',
    'lovers',
    'newbieisland',
    'oldman',
    'outhouse',
    'peerpressure',
    'petra',
    'racedwarf',
    'raceelf',
    'racehuman',
    'racetroll',
    'riddles',
    'salesman',
    'sethsong',
    'smith',
    'soulgem',
    'spa',
    'specialtydarkarts',
    'specialtymysticpower',
    'specialtythiefskills',
    'statue',
    'stocks',
    'stonehenge',
    'strategyhut',
    'thieves',
    'tutor',
    'tynan',
    'waterfall',
];

$stage = (int) httpget('stage');
$session['installer']['stagecompleted'] = $session['installer']['stagecompleted'] ?? -1;
$stage = min($session['installer']['stagecompleted'] + 1, $stage);

if (! isset($session['dbinfo']))
{
    $session['dbinfo'] = [
        'DB_HOST' => '',
        'DB_USER' => '',
        'DB_PASS' => '',
        'DB_NAME' => '',
        'DB_USEDATACACHE' => false,
        'DB_DATACACHEPATH' => 'cache',
        'DB_PREFIX' => ''
    ];
}

if (file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT) && (3 == $stage || 4 == $stage || 5 == $stage))
{
    output('`%This stage was completed during a previous installation.');
    output('`2If you wish to perform stages 4 through 6 again, please delete the file named "%s" from your site.`n`n', \Lotgd\Core\Application::FILE_DB_CONNECT);
    $stage = 6;
}

if ($stage > $session['installer']['stagecompleted'])
{
    $session['installer']['stagecompleted'] = $stage;
}

page_header('LoGD Installer &#151; %s', $stages[$stage] ?? $stages[0]);

switch ($stage)
{
    case 0:
    case 1:
    case 2:
    case 3:
    case 4:
    case 5:
    case 6:
    case 7:
    case 8:
    case 9:
    case 10:
        require_once "lib/installer/installer_stage_$stage.php";
    break;
    default:
        require_once 'lib/installer/installer_stage_default.php';
    break;
}

if (! $noinstallnavs)
{
    if ($session['user']['loggedin'])
    {
        addnav('Back to the game', $session['user']['restorepage']);
    }
    addnav('Install Stages');

    for ($x = 0; $x <= min(count($stages) - 1, $session['installer']['stagecompleted'] + 1); $x++)
    {
        if ($x == $stage)
        {
            $stages[$x] = "`^{$stages[$x]} <----";
        }
        addnav($stages[$x], "installer.php?stage=$x");
    }
}
page_footer(false);
