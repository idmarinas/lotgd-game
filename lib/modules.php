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
function mass_module_prepare($hooknames)
{
    sort($hooknames);
    $Pmodules = DB::prefix('modules');
    $Pmodule_hooks = DB::prefix('module_hooks');
    $Pmodule_settings = DB::prefix('module_settings');
    $Pmodule_userprefs = DB::prefix('module_userprefs');

    global $modulehook_queries;
    global $module_preload;
    global $module_settings;
    global $module_prefs;
    global $session;

    //collect the modules who attach to these hooks.
    $sql =
        "SELECT
			$Pmodule_hooks.modulename,
			$Pmodule_hooks.location,
			$Pmodule_hooks.function,
			$Pmodule_hooks.whenactive
		FROM
			$Pmodule_hooks
		INNER JOIN
			$Pmodules
		ON	$Pmodules.modulename = $Pmodule_hooks.modulename
		WHERE
			active = 1
		AND	location IN ('".join("', '", $hooknames)."')
		ORDER BY
			$Pmodule_hooks.location,
			$Pmodule_hooks.priority,
            $Pmodule_hooks.modulename";
    $result = DB::query($sql);
    $modulenames = [];

    while ($row = DB::fetch_assoc($result))
    {
        $modulenames[$row['modulename']] = $row['modulename'];

        if (! isset($module_preload[$row['location']]))
        {
            $module_preload[$row['location']] = [];
            $modulehook_queries[$row['location']] = [];
        }
        //a little black magic trickery: formatting entries in
        //$modulehook_queries the same way that DB::query_cached
        //returns query results.
        array_push($modulehook_queries[$row['location']], $row);
        $module_preload[$row['location']][$row['modulename']] = $row['function'];
    }
    //SQL IN() syntax for the modules involved here.
    $modulelist = "'".join("', '", $modulenames)."'";

    //Load the settings for the modules on these hooks.
    $sql =
        "SELECT
			modulename,
			setting,
			value
		FROM
			$Pmodule_settings
		WHERE
			modulename IN ($modulelist)";
    $result = DB::query($sql);

    while ($row = DB::fetch_assoc($result))
    {
        $module_settings[$row['modulename']][$row['setting']] = $row['value'];
    }

    //Load the current user's prefs for the modules on these hooks.
    if (! isset($session['user']['acctid']))
    {
        return true;
    }
    // nothing to do if there is no user logged in
    $sql =
        "SELECT
			modulename,
			setting,
			userid,
			value
		FROM
			$Pmodule_userprefs
		WHERE
			modulename IN ($modulelist)
		AND	userid = ".(int) $session['user']['acctid'];
    $result = DB::query($sql);

    while ($row = DB::fetch_assoc($result))
    {
        $module_prefs[$row['userid']][$row['modulename']][$row['setting']] = $row['value'];
    }

    return true;
}

function get_module_info($shortname)
{
    global $mostrecentmodule;

    $moduleinfo = [];

    // Save off the mostrecent module.
    $mod = $mostrecentmodule;

    if (injectmodule($shortname, true))
    {
        $fname = "{$shortname}_getmoduleinfo";

        if (function_exists($fname))
        {
            tlschema("module-$shortname");
            $moduleinfo = $fname();
            tlschema();
            // Don't pick up this text unless we need it.
            if (! isset($moduleinfo['name']) || ! isset($moduleinfo['category']) || ! isset($moduleinfo['author']) || ! isset($moduleinfo['version']))
            {
                $ns = translate_inline('Not specified', 'common');
            }

            if (! isset($moduleinfo['name']))
            {
                $moduleinfo['name'] = "$ns ($shortname)";
            }

            if (! isset($moduleinfo['category']))
            {
                $moduleinfo['category'] = "$ns ($shortname)";
            }

            if (! isset($moduleinfo['author']))
            {
                $moduleinfo['author'] = "$ns ($shortname)";
            }

            if (! isset($moduleinfo['version']))
            {
                $moduleinfo['version'] = '0.0';
            }

            if (! isset($moduleinfo['download']))
            {
                $moduleinfo['download'] = '';
            }

            if (! isset($moduleinfo['description']))
            {
                $moduleinfo['description'] = '';
            }
        }

        if (! is_array($moduleinfo) || count($moduleinfo) < 2)
        {
            $mf = translate_inline('Missing function', 'common');
            $moduleinfo = [
                'name' => "$mf ({$shortname}_getmoduleinfo)",
                'version' => '0.0',
                'author' => "$mf ({$shortname}_getmoduleinfo)",
                'category' => "$mf ({$shortname}_getmoduleinfo)",
                'download' => '',
            ];
        }
    }
    else
    {
        // This module couldn't be injected at all.
        return [];
    }
    $mostrecentmodule = $mod;

    if (! isset($moduleinfo['requires']))
    {
        $moduleinfo['requires'] = [];
    }

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
    $sql = 'LOCK TABLES '.DB::prefix('module_settings').' WRITE';
    DB::query($sql);
}

function module_sem_release()
{
    //please see warnings in module_sem_acquire()
    $sql = 'UNLOCK TABLES';

    DB::query($sql);
}

function module_editor_navs($like, $linkprefix)
{
    $sql = 'SELECT formalname,modulename,active,category FROM '.DB::prefix('modules')." WHERE infokeys LIKE '%|$like|%' ORDER BY category,formalname";
    $result = DB::query($sql);
    $curcat = '';

    while ($row = DB::fetch_assoc($result))
    {
        if ($curcat != $row['category'])
        {
            $curcat = $row['category'];
            addnav(['%s Modules', $curcat]);
        }
        //I really think we should give keyboard shortcuts even if they're
        //susceptible to change (which only happens here when the admin changes
        //modules around).  This annoys me every single time I come in to this page.
        addnav_notl(($row['active'] ? '' : '`)').$row['formalname'].'`0', $linkprefix.$row['modulename']);
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
        $sql = 'SELECT setting, value FROM '.DB::prefix('module_objprefs')." WHERE modulename='$module' AND objtype='$type' AND objid='$id'";
        $result = DB::query($sql);

        while ($row = DB::fetch_assoc($result))
        {
            $data[$row['setting']] = $row['value'];
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
