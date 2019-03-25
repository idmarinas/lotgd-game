<?php

require_once 'lib/installer/installer_functions.php';
require_once 'lib/sanitize.php';

$session['installer']['stagecompleted'] = $stage - 1;

$request = \LotgdLocator::get(\Lotgd\Core\Http::class);
if ($request->isPost())
{
    $session['installer']['moduleoperations'] = \LotgdHttp::getPost('modules') ?: [];
    $session['installer']['stagecompleted'] = $stage;

    return redirect('installer.php?stage='.($stage + 1));
}
elseif (array_key_exists('moduleoperations', $session['installer']) && is_array($session['installer']['moduleoperations']))
{
    $session['installer']['stagecompleted'] = $stage;
}

$phpram = ini_get('memory_limit');
// 12 MBytes
if (return_bytes($phpram) < 12582912 && -1 != $phpram && ! $session['installer']['dbinfo']['upgrade'])
{
    // enter this ONLY if it's not an upgrade and if the limit is really too low

    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('stage8.memory.warning', [], 'page-installer'));
    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('stage8.memory.message', [], 'page-installer'));
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('stage8.memory.error', [], 'page-installer'));

    $session['installer']['stagecompleted'] = 8;
    $session['installer']['skipmodules'] = true;
}

$all_modules = [];
$install_status = get_module_install_status();

$installation = new \Lotgd\Core\Installer\Install();
$recommended_modules = $installation->getRecommendedModules();

//-- Only get the installed modules if it's an update
if ($session['installer']['dbinfo']['upgrade'])
{
    $moduleRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Modules::class);
    $query = $moduleRepository->createQueryBuilder('u');
    $query->addOrderBy('u.category', 'ASC')
        ->addOrderBy('u.active', 'DESC')
        ->addOrderBy('u.formalname', 'ASC')
    ;

    $result = $query->getQuery()->getArrayResult();

    foreach ($result as $row)
    {
        if (! array_key_exists($row['category'], $all_modules))
        {
            $all_modules[$row['category']] = [];
        }
        $row['installed'] = true;
        $all_modules[$row['category']][$row['modulename']] = $row;
    }
}

$uninstalled = $install_status['uninstalledmodules'];
reset($uninstalled);
$invalidmodule = [
    'version' => '',
    'author' => '',
    'category' => 'Invalid Modules',
    'download' => '',
    'description' => '',
    'invalid' => true,
];

//-- Add uninstalled modules
while (list($key, $modulename) = each($uninstalled))
{
    $row = [];

    $moduleinfo = get_module_info($modulename);

    //end of testing
    $row['installed'] = false;
    $row['active'] = false;
    $row['category'] = $moduleinfo['category'];
    $row['modulename'] = $modulename;
    $row['formalname'] = $moduleinfo['name'];
    $row['description'] = $moduleinfo['description'] ?? '';
    $row['author'] = $moduleinfo['author'];
    $row['invalid'] = $moduleinfo['invalid'] ?? false;

    if (! array_key_exists($row['category'], $all_modules))
    {
        $all_modules[$row['category']] = [];
    }
    $all_modules[$row['category']][$row['modulename']] = $row;
}

ksort($all_modules);
reset($all_modules);

if (0 == count($all_modules))
{
    $session['installer']['skipmodules'] = true;
}

$params = [
    'modules' => $all_modules,
    'recommendedModules' => $recommended_modules,
    'isUpgrade' => $session['installer']['dbinfo']['upgrade'],
    'stage' => $stage
];

rawoutput(LotgdTheme::renderLotgdTemplate('core/pages/installer/stage-8.twig', $params));
