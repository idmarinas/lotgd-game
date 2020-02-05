<?php

/**
 * Returns the status of a module as a bitfield.
 *
 * @param string $modulename The module name
 * @param string $version    The version to check for (null for don't care)
 *
 * @return int The status codes for the module
 */
function module_status($modulename, ?string $version = null)
{
    global $injected_modules;

    $modulename = \LotgdSanitize::moduleNameSanitize($modulename);
    $modulefilename = "modules/{$modulename}.php";

    if (file_exists($modulefilename))
    {
        // The module file doesn't exist.
        return MODULE_FILE_NOT_PRESENT;
    }

    // The module isn't installed
    $status = MODULE_NOT_INSTALLED;

    $repository = \Doctrine::getRepository('LotgdCore:Modules');
    $row = $repository->findOneBy([ 'modulename' => $modulename ]);

    if ($row)
    {
        // The module is installed
        $status = MODULE_INSTALLED;

        if ($row->getActive())
        {
            // Module is here and active
            $status |= MODULE_ACTIVE;
            // In this case, the module could have been force injected or
            // not.  We still want to mark it either way.
            if (
                (array_key_exists($modulename, $injected_modules[0]) && $injected_modules[0][$modulename])
                || (array_key_exists($modulename, $injected_modules[1]) && $injected_modules[1][$modulename])
            ) {
                $status |= MODULE_INJECTED;
            }
        }
        // Force-injected modules can be injected but not active.
        elseif (array_key_exists($modulename, $injected_modules[1]) && $injected_modules[1][$modulename])
        {
            $status |= MODULE_INJECTED;
        }

        // Check the version number
        if (! $version)
        {
            $status |= MODULE_VERSION_OK;
        }
        elseif (module_compare_versions($row->getVersion(), $version) < 0)
        {
            $status |= MODULE_VERSION_TOO_LOW;
        }
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

/**
 * Get status of module.
 *
 * @return array
 */
function get_module_install_status(): array
{
    if (! \Doctrine::isConnected())
    {
        return [
            'installedcategories' => [],
            'installedmodules' => [],
            'deactivedmodules' => [],
            'activedmodules' => [],
            'uninstalledmodules' => [],
            'uninstcount' => []
        ];
    }

    // Collect the names of all installed modules.
    $repository = \Doctrine::getRepository('LotgdCore:Modules');
    $result = $repository->findAll();

    $installedModules = [];
    $installedCategories = [];
    $deactivedModules = [];
    $activedModules = [];

    if ($result)
    {
        foreach ($result as $row)
        {
            $installedModules["{$row->getModulename()}.php"] = true;
            ${$row->getActive() ? 'activedModules' : 'deactivedModules'}["{$row->getModulename()}.php"] = true;

            $installedCategories[$row->getCategory()] = ($installedCategories[$row->getCategory()] ?? 0) + 1;
        }
        unset($row);
    }

    $uninstalledModules = [];
    $files = array_map(function ($val) use ($installedModules) {
        if (! isset($installedModules[basename($val)]))
        {
            return $val;
        }
    }, glob('modules/*.php'));
    $files = array_filter($files);

    $uninstalled = count($files);
    if (count($files))
    {
        foreach($files as $file)
        {
            $uninstalledModules[] = basename($file, '.php');
        }
    }

    return [
        'installedcategories' => $installedCategories,
        'installedmodules' => $installedModules,
        'uninstalledmodules' => $uninstalledModules,
        'deactivedmodules' => $deactivedModules,
        'activedmodules' => $activedModules,
        'uninstcount' => $uninstalled
    ];
}
