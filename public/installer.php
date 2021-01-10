<?php

require_once '../lib/installer/installer_functions.php';

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
$stage = (int) \LotgdRequest::getQuery('stage', 0);
$stage = min($session['installer']['stagecompleted'] + 1, $stage, 11);
$session['installer']['stagecompleted'] = max($stage, $session['installer']['stagecompleted']);

//-- Init page
\LotgdResponse::pageStart('title',  [
    'stage' => \LotgdTranslator::t("stages.0{$stage}", [], 'navigation_installer')
], 'page_installer');

if (file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT) && (3 == $stage || 4 == $stage || 5 == $stage))
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('stage8.fileDbConnect.exists', [], 'page_installer'));
    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('stage8.fileDbConnect.info', ['file' => \Lotgd\Core\Application::FILE_DB_CONNECT], 'page_installer'));

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
    \LotgdNavigation::setTextDomain('navigation_installer');
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

//-- Finalize page
\LotgdResponse::pageEnd(false);
