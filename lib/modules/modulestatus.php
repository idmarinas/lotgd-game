<?php

/**
 * Returns the status of a module as a bitfield.
 *
 * @param string $modulename The module name
 * @param string $version    The version to check for (false for don't care)
 *
 * @return int The status codes for the module
 */
function module_status($modulename, $version = false)
{
    global $injected_modules;

    $modulename = modulename_sanitize($modulename);
    $modulefilename = "modules/{$modulename}.php";
    $status = MODULE_NO_INFO;

    if (file_exists($modulefilename))
    {
        $select = DB::select('modules');
        $select->columns(['active', 'filemoddate', 'infokeys', 'version'])
            ->where->equalTo('modulename', $modulename)
        ;
        $result = DB::execute($select);

        if ($result->count() > 0)
        {
            // The module is installed
            $status = MODULE_INSTALLED;
            $row = $result->current();

            if ($row['active'])
            {
                // Module is here and active
                $status |= MODULE_ACTIVE;
                // In this case, the module could have been force injected or
                // not.  We still want to mark it either way.
                if (array_key_exists($modulename, $injected_modules[0]) && $injected_modules[0][$modulename])
                {
                    $status |= MODULE_INJECTED;
                }

                if (array_key_exists($modulename, $injected_modules[1]) && $injected_modules[1][$modulename])
                {
                    $status |= MODULE_INJECTED;
                }
            }
            else
            {
                // Force-injected modules can be injected but not active.
                if (array_key_exists($modulename, $injected_modules[1]) && $injected_modules[1][$modulename])
                {
                    $status |= MODULE_INJECTED;
                }
            }
            // Check the version number
            if (false === $version)
            {
                $status |= MODULE_VERSION_OK;
            }
            else
            {
                if (module_compare_versions($row['version'], $version) < 0)
                {
                    $status |= MODULE_VERSION_TOO_LOW;
                }
                else
                {
                    $status |= MODULE_VERSION_OK;
                }
            }
        }
        else
        {
            // The module isn't installed
            $status = MODULE_NOT_INSTALLED;
        }
    }
    else
    {
        // The module file doesn't exist.
        $status = MODULE_FILE_NOT_PRESENT;
    }

    return $status;
}

/**
 * Determines if a module is activated.
 *
 * @param string $modulename The module name
 *
 * @return bool If the module is active or not
 */
function is_module_active($modulename)
{
    return module_status($modulename) & MODULE_ACTIVE;
}

/**
 * Determines if a module is installed.
 *
 * @param string $modulename The module name
 * @param string $version    The version to check for
 *
 * @return bool If the module is installed
 */
function is_module_installed($modulename, $version = false)
{
    // Status will say the version is okay if we don't care about the
    // version or if the version is actually correct
    return module_status($modulename, $version) & (MODULE_INSTALLED | MODULE_VERSION_OK);
}


function get_module_install_status()
{
    // Collect the names of all installed modules.
    $seenmodules = [];
    $seencats = [];
    $sql = 'SELECT modulename,category FROM '.DB::prefix('modules');
    $result = @DB::query($sql);

    if (false !== $result)
    {
        while ($row = DB::fetch_assoc($result))
        {
            $seenmodules["{$row['modulename']}.php"] = true;

            if (! array_key_exists($row['category'], $seencats))
            {
                $seencats[$row['category']] = 1;
            }
            else
            {
                $seencats[$row['category']]++;
            }
        }
    }

    $uninstmodules = [];

    if ($handle = opendir('modules'))
    {
        $ucount = 0;

        while (false !== ($file = readdir($handle)))
        {
            if ('.' == $file[0])
            {
                continue;
            }

            if (preg_match('/\\.php$/', $file) && ! isset($seenmodules[$file]))
            {
                $ucount++;
                $uninstmodules[] = substr($file, 0, strlen($file) - 4);
            }
        }
    }
    closedir($handle);
    sort($uninstmodules);

    return ['installedcategories' => $seencats, 'installedmodules' => $seenmodules, 'uninstalledmodules' => $uninstmodules, 'uninstcount' => $ucount];
}
