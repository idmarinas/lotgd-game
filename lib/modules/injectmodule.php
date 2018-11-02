<?php

$injected_modules = [1 => [], 0 => []];

function injectmodule($modulename, $force = false)
{
    global $mostrecentmodule, $injected_modules;

    //try to circumvent the array_key_exists() problem we've been having.
    $force = $force ? 1 : 0;

    //early escape if we already called injectmodule this hit with the
    //same args.
    if (isset($injected_modules[$force][$modulename]))
    {
        $mostrecentmodule = $modulename;

        return $injected_modules[$force][$modulename];
    }

    $modulename = modulename_sanitize($modulename);
    $modulefilename = "modules/{$modulename}.php";

    if (file_exists($modulefilename))
    {
        tlschema("module-{$modulename}");
        $select = DB::select('modules');
        $select->columns(['active', 'filemoddate', 'infokeys', 'version'])
            ->where->equalTo('modulename', $modulename)
        ;
        $result = DB::execute($select);

        if (! $force)
        {
            //our chance to abort if this module isn't currently installed
            //or doesn't meet the prerequisites.
            if (0 == $result->count())
            {
                tlschema();
                output_notl('`n`3Module `#%s`3 is not installed, but was attempted to be injected.`n', $modulename);
                $injected_modules[$force][$modulename] = false;

                return false;
            }
            $row = $result->current();

            if (! $row['active'])
            {
                tlschema();
                output('`n`3Module `#%s`3 is not active, but was attempted to be injected.`n', $modulename);
                $injected_modules[$force][$modulename] = false;

                return false;
            }
        }

        require_once $modulefilename;

        $mostrecentmodule = $modulename;
        $info = '';

        if (! $force)
        {
            //avoid calling the function if we're forcing the module
            $fname = $modulename.'_getmoduleinfo';
            $info = $fname();

            $info['requires'] = $info['requires'] ?? [];
            $info['download'] = $info['download'] ?? '';
            $info['description'] = $info['description'] ?? '';

            if (! is_array($info['requires']))
            {
                $info['requires'] = [];
            }

            if (! module_check_requirements($info['requires']))
            {
                $injected_modules[$force][$modulename] = false;
                tlschema();
                output('`n`3Module `#%s`3 does not meet its prerequisites.`n', $modulename);

                return false;
            }
        }

        //check to see if the module needs to be upgraded.
        if ($result->count() > 0)
        {
            $row = $row ?? $result->current();
            $filemoddate = date('Y-m-d H:i:s', filemtime($modulefilename));

            if ($row['filemoddate'] != $filemoddate || '' == $row['infokeys'] || '|' != $row['infokeys'][0] || '' == $row['version'])
            {
                //The file has recently been modified, lock tables and
                //check again (knowing we're the only one who can do this
                //at one shot)
                $sql = sprintf('LOCK TABLES %s WRITE', DB::prefix('modules'));
                DB::query($sql);
                //check again after the table has been locked.
                $select = DB::select('modules');
                $select->columns(['filemoddate'])
                    ->where->equalTo('modulename', $modulename)
                ;
                $row = $result->current();

                if ($row['filemoddate'] != $filemoddate || ! isset($row['infokeys']) || '' == $row['infokeys'] || '|' != $row['infokeys'][0] || '' == $row['version'])
                {
                    //the file mod time is still different from that
                    //recorded in the database, time to update the database
                    //and upgrade the module.
                    debug("The module $modulename was found to have updated, upgrading the module now.");

                    if (! is_array($info))
                    {
                        //we might have gotten this info above, if not,
                        //we need it now.
                        $fname = "{$modulename}_getmoduleinfo";
                        $info = $fname();

                        $info['download'] = $info['download'] ?? '';
                        $info['version'] = $info['version'] ?? '0.0';
                        $info['description'] = $info['description'] ?? '';
                    }
                    //Everyone else will block at the initial lock tables,
                    //we'll update, and on their second check, they'll fail.
                    //Only we will update the table.

                    $update = DB::update('modules');
                    $update->set([
                            'moduleauthor' => $info['author'],
                            'category' => $info['category'],
                            'formalname' => $info['name'],
                            'description' => $info['description'],
                            'filemoddate' => $filemoddate,
                            'infokeys' => sprintf('|%s|', implode(array_keys($info), '|')),
                            'version' => $info['version'],
                            'download' => $info['download']
                        ])
                        ->where->equalTo('modulename', $modulename)
                    ;
                    DB::execute($update);
                    debug(DB::sqlString());

                    DB::query('UNLOCK TABLES');
                    // Remove any old hooks (install will reset them)
                    module_wipehooks();
                    $fname = "{$modulename}_install";

                    if (false === $fname())
                    {
                        return false;
                    }
                    invalidatedatacache("injections-inject-$modulename");
                }
                else
                {
                    DB::query('UNLOCK TABLES');
                }
            }
        }
        tlschema();
        $injected_modules[$force][$modulename] = true;

        return true;
    }
    else
    {
        output('`n`$Module `^%s`$ was not found in the modules directory.`n', $modulename);
        $injected_modules[$force][$modulename] = false;

        return false;
    }
}
