<?php

// translator ready
// addnews ready
// mail ready

require_once 'lib/arraytourl.php';
require_once 'lib/modules/injectmodule.php';
require_once 'lib/modules/modulestatus.php';
require_once 'lib/modules/blockunblock.php';
require_once 'lib/modules/actions.php';
require_once 'lib/modules/settings.php';
require_once 'lib/modules/objpref.php';
require_once 'lib/modules/prefs.php';
require_once 'lib/modules/hook.php';
require_once 'lib/modules/event.php';

/**
 * Checks if the module requirements are satisfied.  Should a module require
 * other modules to be installed and active, then optionally makes them so.
 *
 * @param array $reqs Requirements of a module from _getmoduleinfo()
 *
 * @return bool If successful or not
 */
function module_check_requirements($reqs, $forceinject = false)
{
    // Since we can inject here, we need to save off the module we're on
    global $mostrecentmodule;

    $oldmodule = $mostrecentmodule;
    $result = true;

    if (! is_array($reqs))
    {
        return false;
    }

    // Check the requirements.
    reset($reqs);

    foreach ($reqs as $key => $val)
    {
        $info = explode('|', $val);

        //-- It's need a specific version of LoTGD
        if ('lotgd' == $key)
        {
            $version = explode(' ', \Lotgd\Core\Application::VERSION);

            $comparison = Composer\Semver\Semver::satisfies($version[0], $info[0]);

            if (! $comparison)
            {
                return false;
            }

            continue;
        }

        if (! is_module_installed($key, $info[0]))
        {
            return false;
        }
        // This is actually cheap since we cache the result
        $status = module_status($key);
        // If it's not injected and we should force it, do so.
        if (! ($status & MODULE_INJECTED) && $forceinject)
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
    sort($hooknames);

    global $modulehook_queries;
    global $module_preload;
    global $module_settings;
    global $module_prefs;
    global $session;

    $hookRepository = \Doctrine::getRepository('LotgdCore:ModuleHooks');
    $settingRepository = \Doctrine::getRepository('LotgdCore:ModuleSettings');
    $userRepository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');

    $query = $hookRepository->createQueryBuilder('u');
    $result = $query
        // ->leftJoin('LotgdCore:Modules', 'm', 'with', $query->expr()->eq('m.modulename', 'u.modulename'))
        ->where('u.active = 1 AND u.location IN (:names)')
        ->setParameter('names', $hooknames)
        ->orderBy('u.location')
        ->addOrderBy('u.priority')
        ->addOrderBy('u.modulename')
        ->getQuery()
        ->getResult()
    ;

    foreach ($result as $row)
    {
        $modulenames[$row->getModulename()] = $row->getModulename();

        if (! isset($module_preload[$row->getLocation()]))
        {
            $module_preload[$row->getLocation()] = [];
            $modulehook_queries[$row->getLocation()] = [];
        }
        //a little black magic trickery: formatting entries in
        //$modulehook_queries the same way that DB::query_cached
        //returns query results.
        array_push($modulehook_queries[$row->getLocation()], $row);
        $module_preload[$row->getLocation()][$row->getModulename()] = $row->getFunction();
    }

    $query = $settingRepository->createQueryBuilder('u');

    $result = $query
        ->where('u.modulename IN (:names)')
        ->setParameter('names', $modulelist)
        ->getQuery()
        ->getResult()
    ;

    foreach ($result as $row)
    {
        $module_settings[$row->getModulename()][$row->getSetting()] = $row->getValue();
    }

    //Load the current user's prefs for the modules on these hooks.
    if (! isset($session['user']['acctid']))
    {
        return true;
    }

    $query = $userRepository->createQueryBuilder('u');

    $result = $query
        ->where('u.modulename IN (:names) AND u.userid = :user')
        ->setParameter('names', $modulelist)
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
    if (! injectmodule($shortname, true))
    {
        return [];
    }

    $missingFunctions = [];

    if (! function_exists("{$shortname}_getmoduleinfo"))
    {
        $missingFunctions[] = "{$shortname}_getmoduleinfo";
    }

    if (! function_exists("{$shortname}_install"))
    {
        $missingFunctions[] = "{$shortname}_install";
    }

    if (! function_exists("{$shortname}_uninstall"))
    {
        $missingFunctions[] = "{$shortname}_uninstall";
    }

    $mostrecentmodule = $mod;

    if (count($missingFunctions))
    {
        return [
            'name' => appoencode('`$Invalid Module! Contact Author or check file!`0'),
            'version' => '0.0.0',
            'author' => 'Missing functions ('.implode(', ', $missingFunctions).')',
            'category' => 'Invalid Modules',
            'download' => '',
            'requires' => [],
            'invalid' => true
        ];
    }

    $fname = "{$shortname}_getmoduleinfo";
    tlschema("module-$shortname");
    $moduleinfo = $fname();
    tlschema();

    $moduleinfo['name'] = $moduleinfo['name'] ?? "Not specified ($shortname)";
    $moduleinfo['category'] = $moduleinfo['category'] ?? "Not specified ($shortname)";
    $moduleinfo['author'] = $moduleinfo['author'] ?? "Not specified ($shortname)";
    $moduleinfo['version'] = $moduleinfo['version'] ?? '0.0.0';
    $moduleinfo['download'] = $moduleinfo['download'] ?? '';
    $moduleinfo['description'] = $moduleinfo['description'] ?? '';

    $moduleinfo['requires'] = $moduleinfo['requires'] ?? [];

    return $moduleinfo;
}

function module_sem_acquire()
{
    //DANGER DANGER WILL ROBINSON
    //use of this function can be EXTREMELY DANGEROUS
    //If there is ANY WAY you can avoid using it, I strongly recommend you
    //do so.  That said, I recognize that at times you need to acquire a
    //semaphore so I'll provide a function to accomplish it.

    //PLEASE make sure you call module_sem_release() AS SOON AS YOU CAN.

    //Since Semaphore support in PHP is a compile time option that is off
    //by default, I'll rely on MySQL's semaphore on table lock.  Note this
    //is NOT as efficient as the PHP semaphore because it blocks other
    //things too.
    //If someone is feeling industrious, a smart function that uses the PHP
    //semaphore when available, and otherwise call the MySQL LOCK TABLES
    //code would be sincerely appreciated.

    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and delete in future version. With Doctrine this is not necesary.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $sql = 'LOCK TABLES '.DB::prefix('module_settings').' WRITE';
    DB::query($sql);
}

function module_sem_release()
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0 and delete in future version. With Doctrine this is not necesary.',
        __METHOD__
    ), E_USER_DEPRECATED);

    //please see warnings in module_sem_acquire()
    $sql = 'UNLOCK TABLES';

    DB::query($sql);
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
                'textDomain' => 'navigation-app',
                'params' => [
                    'category' => $curcat
                ]
            ]);
        }
        //I really think we should give keyboard shortcuts even if they're
        //susceptible to change (which only happens here when the admin changes
        //modules around).  This annoys me every single time I come in to this page.
        \LotgdNavigation::addNavNotl(sprintf('%s%s%s',
                $row->getActive() ? '' : '`)',
                $row->getFormalname(),
                $row->getActive() ? '' : '`0'
        ), $linkprefix.$row->getModulename() );
    }
}

function module_objpref_edit($type, $module, $id)
{
    $info = get_module_info($module);

    if (count($info['prefs-'.$type]) > 0)
    {
        $data = [];
        $msettings = [];

        foreach ($info['prefs-'.$type] as $key => $val)
        {
            if (is_array($val))
            {
                $v = $val[0];
                $x = explode('|', $v);
                $val[0] = $x[0];
                $x[0] = $val;
            }
            else
            {
                $x = explode('|', $val);
            }

            $msettings[$key] = $x[0];
            // Set up default
            if (isset($x[1]))
            {
                $data[$key] = $x[1];
            }
        }
        $repository = \Doctrine::getRepository('LotgdCore:ModuleObjprefs');
        $result = $repository->findBy([ 'modulename' => $module,  'objtype' => $type, 'objid' => $id ]);

        foreach ($result as $row)
        {
            $data[$row->getSetting()] = $row->getValue();
        }
        tlschema("module-$module");
        lotgd_showform($msettings, $data);
        tlschema();
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

        return translate_inline($session['user']['race'], 'race');
    }
    else
    {
        return translate_inline($thisuser, 'race');
    }
}

function module_delete_oldvalues($table, $key)
{
    require_once 'lib/gamelog.php';

    $total = 0;
    $res = DB::query('SELECT modulename FROM '.DB::prefix('modules')." WHERE infokeys LIKE '%|$key|%'");

    while ($row = DB::fetch_assoc($res))
    {
        $mod = $row['modulename'];

        require_once "modules/{$mod}.php";

        $func = "{$mod}_getmoduleinfo";
        $info = $func();
        $keys = array_filter(array_keys($info[$key]), 'module_pref_filter');
        $keys = array_map('addslashes', $keys);
        $keys = implode("','", $keys);

        if ($keys)
        {
            DB::query('DELETE FROM '.DB::prefix($table)." WHERE modulename='$mod' AND setting NOT IN ('$keys')");
        }
        $total += DB::affected_rows();
    }

    gamelog("Cleaned up $total old values in $table that don't exist anymore", 'maintenance');
}

function module_pref_filter($a)
{
    return ! is_numeric($a);
}
