<?php

// translator ready
// addnews ready
// mail ready

require_once 'lib/modules/injectmodule.php';
require_once 'lib/modules/modulestatus.php';
require_once 'lib/modules/blockunblock.php';
require_once 'lib/modules/actions.php';
require_once 'lib/modules/settings.php';
require_once 'lib/modules/objpref.php';
require_once 'lib/modules/prefs.php';
require_once 'lib/modules/hook.php';
require_once 'lib/modules/event.php';
require_once 'lib/showform.php';

/**
 * Checks if the module requirements are satisfied.  Should a module require
 * other modules to be installed and active, then optionally makes them so.
 *
 * @param array $reqs        Requirements of a module from _getmoduleinfo()
 * @param mixed $forceinject
 *
 * @return bool If successful or not
 */
function module_check_requirements($reqs, $forceinject = false)
{
    // Since we can inject here, we need to save off the module we're on
    global $mostrecentmodule;

    $oldmodule = $mostrecentmodule;
    $result    = true;

    if ( ! \is_array($reqs))
    {
        return false;
    }

    // Check the requirements.
    \reset($reqs);

    foreach ($reqs as $key => $val)
    {
        $info = \explode('|', $val);

        //-- It's need a specific version of LoTGD
        if ('lotgd' == $key)
        {
            $version = \explode(' ', \Lotgd\Core\Application::VERSION);

            $comparison = Composer\Semver\Semver::satisfies($version[0], $info[0]);

            if ( ! $comparison)
            {
                return false;
            }

            continue;
        }

        if ( ! is_module_installed($key, $info[0]))
        {
            return false;
        }
        // This is actually cheap since we cache the result
        $status = module_status($key);
        // If it's not injected and we should force it, do so.
        if ( ! ($status & MODULE_INJECTED) && $forceinject)
        {
            $result = $result && injectmodule($key);
        }
    }

    $mostrecentmodule = $oldmodule;

    return $result;
}

$module_preload = [];
/**
 * Preloads data for multiple modules in one shot rather than
 * having to make SQL calls for each hook, when many of the hooks
 * are found on every page.
 *
 * @param array $hooknames names of hooks whose attached modules should be preloaded
 *
 * @return bool Success
 */
function mass_module_prepare(array $hooknames)
{
    \sort($hooknames);

    global $modulehook_queries;
    global $module_preload;
    global $module_settings;
    global $module_prefs;
    global $session;

    if ( ! \Doctrine::isConnected())
    {
        return false;
    }

    $hookRepository    = \Doctrine::getRepository('LotgdCore:ModuleHooks');
    $settingRepository = \Doctrine::getRepository('LotgdCore:ModuleSettings');
    $userRepository    = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');

    $query  = $hookRepository->createQueryBuilder('u');
    $result = $query
        ->leftJoin('LotgdCore:Modules', 'm', 'with', $query->expr()->eq('m.modulename', 'u.modulename'))
        ->where('m.active = 1 AND u.location IN (:names)')
        ->setParameter('names', $hooknames)
        ->orderBy('u.location')
        ->addOrderBy('u.priority')
        ->addOrderBy('u.modulename')
        ->getQuery()
        ->getResult()
    ;

    $modulenames = [];

    foreach ($result as $row)
    {
        $modulenames[] = $row->getModulename();

        if ( ! isset($module_preload[$row->getLocation()]))
        {
            $module_preload[$row->getLocation()]     = [];
            $modulehook_queries[$row->getLocation()] = [];
        }
        //a little black magic trickery: formatting entries in
        //$modulehook_queries the same way that DB::query_cached
        //returns query results.
        \array_push($modulehook_queries[$row->getLocation()], $row);
        $module_preload[$row->getLocation()][$row->getModulename()] = $row->getFunction();
    }

    $query = $settingRepository->createQueryBuilder('u');

    $result = $query
        ->where('u.modulename IN (:names)')
        ->setParameter('names', $modulenames)
        ->getQuery()
        ->getResult()
    ;

    foreach ($result as $row)
    {
        $module_settings[$row->getModulename()][$row->getSetting()] = $row->getValue();
    }

    //Load the current user's prefs for the modules on these hooks.
    if ( ! isset($session['user']['acctid']))
    {
        return true;
    }

    $query = $userRepository->createQueryBuilder('u');

    $result = $query
        ->where('u.modulename IN (:names) AND u.userid = :user')
        ->setParameter('names', $modulenames)
        ->setParameter('user', $session['user']['acctid'])
        ->getQuery()
        ->getResult()
    ;

    foreach ($result as $row)
    {
        $module_prefs[$row->getUserid()][$row->getModulename()][$row->getSetting()] = $row->getValue();
    }

    return true;
}

function get_module_info($shortname)
{
    global $mostrecentmodule;

    $moduleinfo = [];

    // Save off the mostrecent module.
    $mod = $mostrecentmodule;

    // This module couldn't be injected at all.
    if ( ! injectmodule($shortname, true))
    {
        return [];
    }

    $missingFunctions = [];

    if ( ! \function_exists("{$shortname}_getmoduleinfo"))
    {
        $missingFunctions[] = "{$shortname}_getmoduleinfo";
    }

    if ( ! \function_exists("{$shortname}_install"))
    {
        $missingFunctions[] = "{$shortname}_install";
    }

    if ( ! \function_exists("{$shortname}_uninstall"))
    {
        $missingFunctions[] = "{$shortname}_uninstall";
    }

    $mostrecentmodule = $mod;

    if (\count($missingFunctions))
    {
        return [
            'name'     => \LotgdFormat::colorize('`$Invalid Module! Contact Author or check file!`0'),
            'version'  => '0.0.0',
            'author'   => 'Missing functions ('.\implode(', ', $missingFunctions).')',
            'category' => 'Invalid Modules',
            'download' => '',
            'requires' => [],
            'invalid'  => true,
        ];
    }

    $fname      = "{$shortname}_getmoduleinfo";
    $moduleinfo = $fname();

    $moduleinfo['name']        = $moduleinfo['name']        ?? "Not specified ({$shortname})";
    $moduleinfo['category']    = $moduleinfo['category']    ?? "Not specified ({$shortname})";
    $moduleinfo['author']      = $moduleinfo['author']      ?? "Not specified ({$shortname})";
    $moduleinfo['version']     = $moduleinfo['version']     ?? '0.0.0';
    $moduleinfo['download']    = $moduleinfo['download']    ?? '';
    $moduleinfo['description'] = $moduleinfo['description'] ?? '';
    $moduleinfo['modulename']  = $shortname;

    $moduleinfo['requires'] = $moduleinfo['requires'] ?? [];

    return $moduleinfo;
}

function module_editor_navs($like, $linkprefix)
{
    $repository = \Doctrine::getRepository('LotgdCore:Modules');

    $result = $repository->findModulesEditorNav($like);

    $curcat = '';

    foreach ($result as $row)
    {
        if ($curcat != $row->getCategory())
        {
            $curcat = $row->getCategory();
            \LotgdNavigation::addHeader('modules.nav.category', [
                'textDomain' => 'navigation_app',
                'params'     => [
                    'category' => $curcat,
                ],
            ]);
        }
        //I really think we should give keyboard shortcuts even if they're
        //susceptible to change (which only happens here when the admin changes
        //modules around).  This annoys me every single time I come in to this page.
        \LotgdNavigation::addNavNotl(\sprintf(
            '%s%s%s',
            $row->getActive() ? '' : '`)',
            $row->getFormalname(),
            $row->getActive() ? '' : '`0'
        ), $linkprefix.$row->getModulename());
    }
}

function module_objpref_edit($type, $module, $id)
{
    $info = get_module_info($module);

    $data       = [];
    $repository = \Doctrine::getRepository('LotgdCore:ModuleObjprefs');
    $result     = $repository->findBy(['modulename' => $module,  'objtype' => $type, 'objid' => $id]);

    foreach ($result as $row)
    {
        $data[$row->getSetting()] = $row->getValue();
    }

    if (\is_string($info["prefs-{$type}"]))
    {
        $form = \LotgdLocation::get($info["prefs-{$type}"]);
        $form->setAttribute('method', 'POST');
        $form->setAttribute('autocomplete', 'off');
        $form->setAttribute('class', 'ui form');

        $form->setData($data);

        return $form;
    }
    elseif (\is_array($info["prefs-{$type}"]) && \count($info["prefs-{$type}"]) > 0)
    {
        \trigger_error(\sprintf(
            'Usage of %s array old style is obsolete since 4.3.0; and delete in version 5.0.0, use new "Laminas Form style" to configure prefs',
            "prefs-{$type}"
        ), E_USER_DEPRECATED);

        $msettings = [];

        foreach ($info["prefs-{$type}"] as $key => $val)
        {
            if (\is_array($val))
            {
                $v      = $val[0];
                $x      = \explode('|', $v);
                $val[0] = $x[0];
                $x[0]   = $val;
            }
            else
            {
                $x = \explode('|', $val);
            }

            $msettings[$key] = $x[0];
            // Set up default
            if (isset($x[1]))
            {
                $data[$key] = $x[1];
            }
        }

        return lotgd_showform($msettings, $data, false, false, false);
    }
}

function module_compare_versions($a, $b)
{
    //this function returns -1 when $a < $b, 1 when $a > $b, and 0 when $a == $b
    //insert alternate version detection and comparison algorithms here.

    //default case, typecast as float
    $a = (float) $a;
    $b = (float) $b;

    return $a < $b ? -1 : ($a > $b ? 1 : 0);
}

/**
 * Evaluates a PHP Expression.
 *
 * @param string $condition The PHP condition to evaluate
 *
 * @return bool The result of the evaluated expression
 */
function module_condition($condition)
{
    global $session;

    $result = eval($condition);

    return (bool) $result;
}

function get_racename($thisuser = true)
{
    if (true === $thisuser)
    {
        global $session;

        return \LotgdTranslator::t('character.racename', [], $session['user']['race']);
    }

    return \LotgdTranslator::t('character.racename', [], $thisuser);
}

function module_pref_filter($a)
{
    return ! \is_numeric($a);
}
