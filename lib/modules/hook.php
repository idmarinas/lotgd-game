<?php

/**
 * An event that should be triggered.
 *
 * @param string $hookname      The name of the event to raise
 * @param array  $args          Arguments that should be passed to the event handler
 * @param bool   $allowinactive Allow inactive modules
 * @param bool   $only          Only this module?
 *
 * @return array The args modified by the event handlers
 */
$currenthook = '';
function modulehook($hookname, $args = false, $allowinactive = false, $only = false)
{
    global $navsection, $mostrecentmodule;
    global $blocked_modules, $block_all_modules, $unblocked_modules;
    global $session, $modulehook_queries;
    global $currenthook;

    $lasthook = $currenthook;
    $currenthook = $hookname;
    static $hookcomment = [];

    if (false === $args)
    {
        $args = [];
    }
    $active = '';

    if (! $allowinactive)
    {
        $active = ' '.DB::prefix('modules').'.active=1 AND';
    }

    if (! is_array($args))
    {
        $where = $mostrecentmodule;

        if (! $where)
        {
            $where = httpGetServer('SCRIPT_NAME');
        }
        debug("Args parameter to modulehook $hookname from $where is not an array.");
    }

    if (isset($session['user']['superuser']) && $session['user']['superuser'] & SU_DEBUG_OUTPUT && ! isset($hookcomment[$hookname]))
    {
        rawoutput("<!--Module Hook: $hookname; allow inactive: ".($allowinactive ? 'true' : 'false').'; only this module: '.(false !== $only ? $only : 'any module'));

        if (! is_array($args))
        {
            $arg = $args.' (NOT AN ARRAY!)';
            rawoutput("  arg: $arg");
        }
        else
        {
            reset($args);

            foreach ($args as $key => $val)
            {
                $arg = $key.' = ';

                if (is_array($val))
                {
                    $arg .= 'array('.count($val).')';
                }
                elseif (is_object($val))
                {
                    $arg .= 'object('.get_class($val).')';
                }
                else
                {
                    $arg .= htmlentities(substr($val, 0, 25), ENT_COMPAT, getsetting('charset', 'UTF-8'));
                }

                rawoutput("  arg: $arg");
            }
        }
        rawoutput('  -->');
        $hookcomment[$hookname] = true;
    }

    if (isset($modulehook_queries[$hookname]) //This data was pre fetched in mass_module_prepare
        && false == $allowinactive //We only ever prefetch for active modules, if we're doing inactive, do the regular query.
        ) {
        $result = $modulehook_queries[$hookname];
    }
    else
    {
        $sql =
            'SELECT
                '.DB::prefix('module_hooks').'.modulename,
                '.DB::prefix('module_hooks').'.location,
                '.DB::prefix('module_hooks').'.function,
                '.DB::prefix('module_hooks').'.whenactive
            FROM
                '.DB::prefix('module_hooks').'
            INNER JOIN
                '.DB::prefix('modules').'
            ON	'.DB::prefix('modules').'.modulename = '.DB::prefix('module_hooks').".modulename
            WHERE
                $active
                ".DB::prefix('module_hooks').".location='$hookname'
            ORDER BY
                ".DB::prefix('module_hooks').'.priority,
                '.DB::prefix('module_hooks').'.modulename';
        $result = DB::query($sql);
    }
    // $args is an array passed by value and we take the output and pass it
    // back through
    // Try at least and fix up a bogus arg so it doesn't cause additional
    // problems later.
    if (! is_array($args))
    {
        $args = ['bogus_args' => $args];
    }

    // Save off the mostrecent module since having that change can change
    // behaviour especially if a module calls modulehooks itself or calls
    // library functions which cause them to be called.
    $mod = $mostrecentmodule;

    while ($row = DB::fetch_assoc($result))
    {
        // If we are only running hooks for a specific module, skip all
        // others.
        if (false !== $only && $row['modulename'] != $only)
        {
            continue;
        }
        // Skip any module invocations which should be blocked.

        if (! array_key_exists($row['modulename'], $blocked_modules))
        {
            $blocked_modules[$row['modulename']] = false;
        }

        if (! array_key_exists($row['modulename'], $unblocked_modules))
        {
            $unblocked_modules[$row['modulename']] = false;
        }

        if (($block_all_modules || $blocked_modules[$row['modulename']]) && ! $unblocked_modules[$row['modulename']])
        {
            continue;
        }

        if (injectmodule($row['modulename'], $allowinactive))
        {
            $oldnavsection = $navsection;
            tlschema("module-{$row['modulename']}");
            // Pass the args into the function and reassign them to the
            // result of the function.
            // Note: each module gets the previous module's modified return
            // value if more than one hook here.
            // Order of operations could become an issue, modules are called
            // in alphabetical order by their module name (not display name).

            // Test the condition code
            if (! array_key_exists('whenactive', $row))
            {
                $row['whenactive'] = '';
            }
            $cond = trim($row['whenactive']);

            if ('' == $cond || true == module_condition($cond))
            {
                // call the module's hook code
                //before, this was just string switching, NOW we make new objects everytime Oo craaaazy load, I am removing this. if you want to collapse, put it in, it's a MODULE

                /*******************************************************/
                $starttime = microtime(true);
                /*******************************************************/
                if (function_exists($row['function']))
                {
                    $res = $row['function']($hookname, $args);
                }
                else
                {
                    trigger_error("Unknown function {$row['function']} for hookname $hookname in module {$row['module']}.", E_USER_WARNING);
                }
                /*******************************************************/
                $endtime = microtime(true);

                if (($endtime - $starttime >= 1.00 && ($session['user']['superuser'] & SU_DEBUG_OUTPUT)))
                {
                    debug('Slow Hook ('.round($endtime - $starttime, 2)."s): $hookname - {$row['modulename']}`n");
                }

                if (getsetting('debug', 0))
                {
                    $sql = 'INSERT INTO '.DB::prefix('debug')." VALUES (0,'hooktime','".$hookname."','".$row['modulename']."','".($endtime - $starttime)."');";
                    DB::query($sql);
                }
                /*******************************************************/
                // test to see if we had any output and if the module allows
                // us to collapse it

                if (! is_array($res))
                {
                    trigger_error("<b>{$row['function']}</b> did not return an array in the module <b>{$row['modulename']}</b> for hook <b>$hookname</b>.", E_USER_WARNING);
                    $res = $args;
                }

                // Clear the collapse flag
                unset($res['nocollapse']);
                //handle return arguments.
                if (is_array($res))
                {
                    $args = $res;
                }
            }

            //revert the translation namespace
            tlschema();
            //revert nav section after we're done here.
            $navsection = $oldnavsection;
        }
    }

    $mostrecentmodule = $mod;
    $currenthook = $lasthook;

    // And hand them back so they can be used.
    return $args;
}

function module_wipehooks()
{
    global $mostrecentmodule;

    //lock the module hooks table.
    $sql = 'LOCK TABLES '.DB::prefix('module_hooks').' WRITE';
    DB::query($sql);

    //invalidate data caches for module hooks associated with this module.
    $select = DB::select('module_hooks');
    $select->columns(['location'])
        ->where->equalTo('modulename', $mostrecentmodule)
    ;
    $result = DB::execute($select);

    while ($row = DB::fetch_assoc($result))
    {
        invalidatedatacache('hooks-hook-'.$row['location']);
    }
    invalidatedatacache('moduleprepare');

    debug("Removing all hooks for $mostrecentmodule");
    $delete = DB::delete('module_hooks');
    $delete->where->equalTo('modulename', $mostrecentmodule);
    DB::execute($delete);
    //unlock the module hooks table.
    DB::query('UNLOCK TABLES');

    $delete = DB::delete('module_event_hooks');
    $delete->where->equalTo('modulename', $mostrecentmodule);
    DB::execute($delete);
}

function module_addeventhook($type, $chance)
{
    global $mostrecentmodule;

    debug("Adding an event hook on $type events for $mostrecentmodule");
    $delete = DB::delete('module_event_hooks');
    $delete->where->equalTo('modulename', $mostrecentmodule)
        ->equalTo('event_type', $type)
    ;
    DB::execute($delete);

    $insert = DB::insert('module_event_hooks');
    $insert->values([
        'event_type' => $type,
        'modulename' => $mostrecentmodule,
        'event_chance' => $chance
    ]);
    DB::execute($insert);

    invalidatedatacache("event-$type");
}

function module_drophook($hookname, $functioncall = false)
{
    global $mostrecentmodule;

    if (false === $functioncall)
    {
        $functioncall = "{$mostrecentmodule}_dohook";
    }

    $delete = DB::delete('module_hooks');
    $delete->where->equalTo('modulename', $mostrecentmodule)
        ->equalTo('location', $hookname)
        ->equalTo('function', $functioncall)
    ;
    DB::execute($delete);
    invalidatedatacache("hooks-hook-$hookname");
    invalidatedatacache('moduleprepare');
}

/**
 * Called by modules to register themselves for a game module hook point, with default priority.
 * Modules with identical priorities will execute alphabetically.  Modules can only have one hook on a given hook name,
 * even if they call this function multiple times, unless they specify different values for the functioncall argument.
 *
 * @param string $hookname     The hook to receive a notification for
 * @param string $functioncall The function that should be called, if not specified, use {modulename}_dohook() as the function
 * @param string $whenactive   an expression that should be evaluated before triggering the event, if not specified, none
 */
function module_addhook($hookname, $functioncall = false, $whenactive = false)
{
    module_addhook_priority($hookname, 50, $functioncall, $whenactive);
}

/**
 * Called by modules to register themselves for a game module hook point, with a given priority -- lower numbers execute first.
 * Modules with identical priorities will execute alphabetically.  Modules can only have one hook on a given hook name,
 * even if they call this function multiple times, unless they specify different values for the functioncall argument.
 *
 * @param string $hookname     The hook to receive a notification for
 * @param int    $priority     The priority for this hooking -- lower numbers execute first.  < 50 means earlier-than-normal execution, > 50 means later than normal execution.  Priority only affects execution order compared to other events registered on the same hook, all events on a given hook will execute before the game resumes execution.
 * @param string $functioncall The function that should be called, if not specified, use {modulename}_dohook() as the function
 * @param string $whenactive   an expression that should be evaluated before triggering the event, if not specified, none
 */
function module_addhook_priority($hookname, $priority = 50, $functioncall = false, $whenactive = false)
{
    global $mostrecentmodule;

    module_drophook($hookname, $functioncall);

    if (false === $functioncall)
    {
        $functioncall = "{$mostrecentmodule}_dohook";
    }

    if (false === $whenactive)
    {
        $whenactive = '';
    }

    debug("Adding a hook at $hookname for $mostrecentmodule to $functioncall which is active on condition '$whenactive'");
    //we want to do a replace in case there's any garbage left in this table which might block new clean data from going in.
    //normally that won't be the case, and so this doesn't have any performance implications.
    $sql = 'REPLACE INTO '.DB::prefix('module_hooks')." (modulename,location,function,whenactive,priority) VALUES ('$mostrecentmodule','".addslashes($hookname)."','".addslashes($functioncall)."','".addslashes($whenactive)."','".addslashes($priority)."')";
    DB::query($sql);
    invalidatedatacache("hooks-hook-$hookname");
    invalidatedatacache('moduleprepare');
}
