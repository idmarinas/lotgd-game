<?php

require_once '../lib/installer/installer_functions.php';

/**
 * Checking basic prerequisites for LOTGD.
 */

$requirements_met = true;
$php_met = true;
$memory_met = true;
$execution_met = true;

//-- Need 128M
if (return_bytes(ini_get('memory_limit')) < 128 * 1024 * 1024)
{
    $requirements_met = false;
    $memory_met = false;
}

$executionTime = ini_get('max_execution_time');
//-- For avoid error when execute xDebug or similar
if ($executionTime < 30 && 0 != $executionTime)
{
    $requirements_met = false;
    $execution_met = false;
}

//-- PHP 7.1 or better is required for this version
if (version_compare(PHP_VERSION, '7.1.0', '<'))
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

chdir(realpath(__DIR__ . '/..'));

define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);
define('IS_INSTALLER', true);

//-- Need because this check is before include common.php
require_once 'vendor/autoload.php'; //-- Autoload class

if (! file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
{
    define('DB_NODB', true);
}

require_once 'common.php';

//-- Initialice install session data
$session['installer'] = $session['installer'] ?? [];
$session['installer']['dbinfo'] = $session['installer']['dbinfo'] ?? [
    'DB_HOST' => '',
    'DB_USER' => '',
    'DB_PASS' => '',
    'DB_NAME' => '',
    'DB_PREFIX' => ''
];

$noinstallnavs = false;

invalidatedatacache('gamesettings');

tlschema('installer');

$stages = [
    'stages.00',
    'stages.01',
    'stages.02',
    'stages.03',
    'stages.04',
    'stages.05',
    'stages.06',
    'stages.07',
    'stages.08',
    'stages.09',
    'stages.010',
    'stages.011',
];

$session['installer']['stagecompleted'] = $session['installer']['stagecompleted'] ?? -1;
$stage = (int) \LotgdHttp::getQuery('stage', 0);
$stage = min($session['installer']['stagecompleted'] + 1, $stage, 11);
$session['installer']['stagecompleted'] = max($stage, $session['installer']['stagecompleted']);

page_header('title', [
    'stage' => \LotgdTranslator::t("stages.0{$stage}", [], 'navigation-installer')
], 'page-installer');

if (file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT) && (3 == $stage || 4 == $stage || 5 == $stage))
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('stage8.fileDbConnect.exists', [], 'page-installer'));
    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('stage8.fileDbConnect.info', ['file' => \Lotgd\Core\Application::FILE_DB_CONNECT], 'page-installer'));

    $stage = 6;
    $session['installer']['stagecompleted'] = $stage;
}

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
    \LotgdNavigation::setTextDomain('navigation-installer');
    if ($session['user']['loggedin'] ?? false)
    {
        \LotgdNavigation::addNav('backToGame', $session['user']['restorepage']);
    }
    \LotgdNavigation::addHeader('stagesCategory');

    $current = min(count($stages) - 1, $session['installer']['stagecompleted'] + 1);
    for ($x = 0; $x <= $current; $x++)
    {
        $options = [];
        if ($x == $stage)
        {
            $options = ['current' => ['open' => '`^', 'close' => '`0']];
        }
        \LotgdNavigation::addNav($stages[$x], "installer.php?stage=$x", $options);
    }
    \LotgdNavigation::setTextDomain();
}

bdump($session['installer']);
page_footer(false);
