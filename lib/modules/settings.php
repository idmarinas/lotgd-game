<?php

/**
 * An associative array of all the settings for the given module.
 *
 * @param string $module
 *
 * @return array
 */
function get_all_module_settings($module = false): array
{
    global $mostrecentmodule;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    return load_module_settings($module);
}

/**
 * Get value for setting of a module.
 */
function get_module_setting($name, $module = false)
{
    global $mostrecentmodule;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    $module_settings = load_module_settings($module);

    if (isset($module_settings[$name]))
    {
        return $module_settings[$name];
    }
    else
    {
        $info = get_module_info($module);

        if (isset($info['settings'][$name]))
        {
            if (is_array($info['settings'][$name]))
            {
                $v = $info['settings'][$name][0];
                $x = explode('|', $v);
            }
            else
            {
                $x = explode('|', $info['settings'][$name]);
            }

            if (isset($x[1]))
            {
                //-- Set and return default setting
                set_module_setting($name, $x[1], $module);

                return $x[1];
            }
        }

        return;
    }
}

/**
 * Set value for setting of a module.
 *
 * @param string $name
 * @param mixed  $value
 * @param string $module
 */
function set_module_setting($name, $value, $module = false)
{
    if ('showFormTabIndex' == $name)
    {
        return;
    }

    global $mostrecentmodule;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }
    $module_settings = load_module_settings($module);

    if (isset($module_settings[$name]))
    {
        $update = DB::update('module_settings');
        $update->set(['value' => $value])
            ->where->equalTo('modulename', $module)
                ->equalTo('setting', $name)
        ;
        DB::execute($update);
    }
    else
    {
        $insert = DB::insert('module_settings');
        $insert->values([
            'modulename' => $module,
            'setting' => $name,
            'value' => $value
        ]);
        DB::execute($insert);
    }
    invalidatedatacache("module-settings-$module", true);
}

/**
 * Increment value for a setting of module.
 *
 * @param string    $name
 * @param float|int $value
 * @param string    $module
 */
function increment_module_setting($name, $value = 1, $module = false)
{
    global $mostrecentmodule;

    $value = (float) $value;//

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }
    $module_settings = load_module_settings($module);

    if (isset($module_settings[$name]))
    {
        $update = DB::update('module_settings');
        $update->set(['value' => DB::expression("value+$value")])
            ->where->equalTo('modulename', $module)
                ->equalTo('setting', $name)
        ;
        DB::execute($update);
    }
    else
    {
        $insert = DB::insert('module_settings');
        $insert->values([
            'modulename' => $module,
            'setting' => $name,
            'value' => $value
        ]);
        DB::execute($insert);
    }
    invalidatedatacache("module-settings-$module", true);
}

/**
 * Clear settings of a module.
 *
 * @param string $module
 */
function clear_module_settings($module = false)
{
    global $mostrecentmodule;

    if (false === $module)
    {
        $module = $mostrecentmodule;
    }

    debug("Deleted module settings cache for $module.");
    invalidatedatacache("module-settings-$module");
}

/**
 * Load settings of a module.
 *
 * @param string $module
 *
 * @return array
 */
function load_module_settings($module): array
{
    $module_settings = datacache("module-settings-$module", 86400, true);

    if (! is_array($module_settings))
    {
        $module_settings = [];
        $select = DB::select('module_settings');
        $select->where->equalTo('modulename', $module);
        $result = DB::execute($select);

        while ($row = DB::fetch_assoc($result))
        {
            $module_settings[$row['setting']] = $row['value'];
        }//end while

        updatedatacache("module-settings-$module", $module_settings, true);
    }//end if

    return $module_settings;
}
